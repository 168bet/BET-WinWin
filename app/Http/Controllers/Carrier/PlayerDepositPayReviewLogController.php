<?php

namespace App\Http\Controllers\Carrier;

use App\DataTables\Carrier\PlayerDepositPayReviewLogDataTable;
use App\Exceptions\CarrierRuntimeException;
use App\Helpers\Caches\PlayerInfoCacheHelper;
use App\Http\Requests\Carrier\CreatePlayerDepositPayLogRequest;
use App\Http\Requests\Carrier\UpdatePlayerDepositPayLogRequest;
use App\Jobs\JudgePlayerHasAutoJoinActivity;
use App\Jobs\PlayerUpgradeLevelHandle;
use App\Models\CarrierActivity;
use App\Models\CarrierActivityAudit;
use App\Models\CarrierPayChannel;
use App\Models\Log\AgentBearUndertakenLog;
use App\Models\Log\CarrierQuotaConsumptionLog;
use App\Models\Log\PlayerAccountLog;
use App\Models\Log\PlayerDepositPayLog;
use App\Models\Log\PlayerWithdrawFlowLimitLog;
use App\Repositories\Carrier\PlayerDepositPayLogRepository;
use Carbon\Carbon;
use Flash;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Response;

class PlayerDepositPayReviewLogController extends AppBaseController
{
    /** @var  PlayerDepositPayLogRepository */
    private $playerDepositPayLogRepository;

    public function __construct(PlayerDepositPayLogRepository $playerDepositPayLogRepo)
    {
        $this->playerDepositPayLogRepository = $playerDepositPayLogRepo;
    }

    /**
     * Display a listing of the PlayerDepositPayLog.
     *
     * @param PlayerDepositPayReviewLogDataTable $PlayerDepositPayReviewLogDataTable
     * @return Response
     */
    public function index(PlayerDepositPayReviewLogDataTable $PlayerDepositPayReviewLogDataTable)
    {
        return $PlayerDepositPayReviewLogDataTable->render('Carrier.player_deposit_pay_review_logs.index');
    }

    /**
     * Show the form for creating a new PlayerDepositPayLog.
     *
     * @return Response
     */
    public function create()
    {
        return view('Carrier.player_deposit_pay_review_logs.create');
    }

    /**
     * Store a newly created PlayerDepositPayLog in storage.
     *
     * @param CreatePlayerDepositPayLogRequest $request
     *
     * @return Response
     */
    public function store(CreatePlayerDepositPayLogRequest $request)
    {
        $input = $request->all();

        $playerDepositPayLog = $this->playerDepositPayLogRepository->create($input);

        Flash::success('Player Deposit Pay Log saved successfully.');

        return redirect(route('playerDepositPayLogs.index'));
    }

    /**
     * Display the specified PlayerDepositPayLog.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $playerDepositPayLog = $this->playerDepositPayLogRepository->findWithoutFail($id);

        if (empty($playerDepositPayLog)) {
            Flash::error('Player Deposit Pay Log not found');

            return redirect(route('playerDepositPayLogs.index'));
        }
        return view('Carrier.player_deposit_pay_review_logs.show')->with('playerDepositPayLog', $playerDepositPayLog);
    }


    /**
     * @param $id
     * @return mixed
     */
    public function showReviewDepositLogModal($id){
        $playerDepositPayLog = $this->playerDepositPayLogRepository->with(['player','playerBankCard','relatedCarrierActivity'])->findWithoutFail($id);
        if (empty($playerDepositPayLog)) {
            return $this->sendNotFoundResponse();
        }
        return view('Carrier.player_deposit_pay_review_logs.review')->with('playerDepositPayLog',$playerDepositPayLog);
    }


    /**
     * 会员存款审核
     * @param $id
     * @param Request $request
     */
    public function reviewDepositLog($id, Request $request){
        $playerDepositPayLog = $this->playerDepositPayLogRepository->with(['carrierPayChannel','player.agent.agentLevel'])->findWithoutFail($id);
        if (empty($playerDepositPayLog)) {
            return $this->sendNotFoundResponse();
        }
        if($playerDepositPayLog->canReview() == true){
            return $this->sendErrorResponse('该订单不能审核');
        }
        $this->validate($request,[
            'is_received_deposit_amount' => 'required|in:0,1'
        ]);
        $isReceiveDepositAmount = $request->get('is_received_deposit_amount');
        if($isReceiveDepositAmount){
            try{
                \DB::transaction(function () use ($playerDepositPayLog,$request){
                    //新增取款流水限制数据
                    $playerDepositPayLog->status = PlayerDepositPayLog::ORDER_STATUS_PAY_SUCCEED;
                    //计算存款优惠,更新玩家主账户余额
                    $depositBenefit = $playerDepositPayLog->amount * $playerDepositPayLog->carrierPayChannel->default_preferential_ratio * 0.01;
                    $playerDepositPayLog->benefit_amount = $depositBenefit;
                    //计算手续费
                    $depositFee = $playerDepositPayLog->amount * $playerDepositPayLog->carrierPayChannel->fee_ratio * 0.01;
                    $feeBearType = $playerDepositPayLog->carrierPayChannel->fee_bear_id;
                    $carrierPayChannel = $playerDepositPayLog->carrierPayChannel;
                    //如果是公司承担手续费或者代理是公司默认代理,或者代理不是佣金代理, 那么还是由公司承担手续费
                    if($feeBearType == CarrierPayChannel::FEE_BEAR_COMPANY || $playerDepositPayLog->player->agent->isCarrierDefaultAgent() || $playerDepositPayLog->player->agent->agentLevel->isCommissionAgent() == false){
                        //如果是公司承担手续费, 那么需要增加公司资金流水
                        $playerDepositPayLog->finally_amount = $playerDepositPayLog->amount + $depositBenefit;
                        if($depositFee > 0){
                            $companyFeeLog = new CarrierQuotaConsumptionLog();
                            $companyFeeLog->amount = -$depositFee;
                            $companyFeeLog->related_pay_channel = $playerDepositPayLog->carrier_pay_channel;
                            $carrierPayChannel->balance -= $depositFee;
                            $companyFeeLog->pay_channel_remain_amount = $carrierPayChannel->balance;
                            if($carrierPayChannel->balance < 0){
                                throw new \Exception('该卡不足以承担这笔存款的手续费');
                            }
                            $companyFeeLog->consumption_source = '会员存款手续费';
                            $companyFeeLog->carrier_id = \WinwinAuth::carrierUser()->carrier_id;
                            $companyFeeLog->created_at  = Carbon::now()->subSeconds(3);
                            $companyFeeLog->save();
                        }
                    }
                    //如果是代理承担并且是佣金代理才能计算佣金承担数据
                    else if($feeBearType == CarrierPayChannel::FEE_BEAR_AGENT && $playerDepositPayLog->player->agent->isCarrierDefaultAgent() == false && $playerDepositPayLog->player->agent->agentLevel->isCommissionAgent()){
                        $playerDepositPayLog->finally_amount = $playerDepositPayLog->amount + $depositBenefit;
                        //如果是代理承担手续费,那么需要新增代理承担记录
                        $agentUnderTakeLog = new AgentBearUndertakenLog();
                        //获取最大承担比例;
                        $conf = $playerDepositPayLog->player->agent->agentLevel->commissionAgentConf;
                        //计算手续费承担比例
                        $agentUnderTakeAmount = $conf->deposit_fee_undertake_ratio * $depositFee * 0.01;
                        $maxOverflowUnderTakeAmount = $conf->deposit_fee_undertake_max > 0 ? $agentUnderTakeAmount - $conf->deposit_fee_undertake_max : 0;
                        //计算运营商承担数据
                        $carrierUnderTakeAmount = $depositFee - $agentUnderTakeAmount - ($maxOverflowUnderTakeAmount > 0 ? $maxOverflowUnderTakeAmount : 0);
                        if($carrierUnderTakeAmount > 0){
                            $playerDepositPayLog->finally_amount = $playerDepositPayLog->amount + $depositBenefit;
                            $companyFeeLog = new CarrierQuotaConsumptionLog();
                            $companyFeeLog->amount = -$carrierUnderTakeAmount;
                            $companyFeeLog->related_pay_channel = $playerDepositPayLog->carrier_pay_channel;
                            $carrierPayChannel->balance -= $carrierUnderTakeAmount;
                            $companyFeeLog->pay_channel_remain_amount = $carrierPayChannel->balance;
                            if($carrierPayChannel->balance < 0){
                                throw new \Exception('该卡不足以承担这笔存款的手续费');
                            }
                            $companyFeeLog->remark = '代理承担会员存款手续费超出最大承担额度,这部分手续费由运营商承担';
                            $companyFeeLog->consumption_source = '会员存款手续费';
                            $companyFeeLog->created_at = Carbon::now()->subSeconds(2);
                            $companyFeeLog->carrier_id = \WinwinAuth::carrierUser()->carrier_id;
                            $companyFeeLog->save();
                        }
                        $agentUnderTakeLog->amount = $agentUnderTakeAmount > $conf->deposit_fee_undertake_max ? $conf->deposit_fee_undertake_max : $agentUnderTakeAmount;
                        $agentUnderTakeLog->agent_id = $playerDepositPayLog->player->agent->id;
                        $agentUnderTakeLog->carrier_id = $playerDepositPayLog->player->carrier_id;
                        $agentUnderTakeLog->undertaken_type = AgentBearUndertakenLog::UNDERTAKEN_TYPE_DEPOSIT_FEE;
                        $agentUnderTakeLog->amount > 0 &&  $agentUnderTakeLog->save();
                    }else if($feeBearType == CarrierPayChannel::FEE_BEAR_PLAYER){
                        $playerDepositPayLog->fee_amount = $depositFee;
                        $playerDepositPayLog->finally_amount = $playerDepositPayLog->amount + $depositBenefit - $depositFee;
                    }
                    //计算存款优惠承担,更新银行卡余额, 如果是佣金代理, 那么佣金代理承担存款优惠;
                    if($depositBenefit > 0){
                        if($playerDepositPayLog->player->agent->isCommissionAgent()){
                            $agentUnderTakeLog = new AgentBearUndertakenLog();
                            //获取最大承担比例;
                            $conf = $playerDepositPayLog->player->agent->agentLevel->commissionAgentConf;
                            //计算优惠承担比例
                            $agentUnderTakeAmount = $conf->deposit_preferential_undertake_ratio * $depositBenefit * 0.01;
                            $maxOverflowUnderTakeAmount =   $conf->deposit_preferential_undertake_max > 0 ? $agentUnderTakeAmount - $conf->deposit_preferential_undertake_max : 0;
                            //计算运营商承担数据
                            $carrierUnderTakeAmount = $depositBenefit - $agentUnderTakeAmount - ($maxOverflowUnderTakeAmount > 0 ? $maxOverflowUnderTakeAmount : 0);
                            if($carrierUnderTakeAmount > 0){
                                $companyFeeLog = new CarrierQuotaConsumptionLog();
                                $companyFeeLog->amount = -$carrierUnderTakeAmount;
                                $companyFeeLog->related_pay_channel = $playerDepositPayLog->carrier_pay_channel;
                                $carrierPayChannel->balance -= $carrierUnderTakeAmount;
                                $companyFeeLog->pay_channel_remain_amount = $carrierPayChannel->balance;
                                if($carrierPayChannel->balance < 0){
                                    throw new \Exception('该卡不足以承担这笔存款的存款优惠');
                                }
                                $companyFeeLog->remark = '代理承担会员存款优惠超出最大承担额度,这部分存款优惠由运营商承担';
                                $companyFeeLog->consumption_source = '会员存款优惠';
                                $companyFeeLog->carrier_id = \WinwinAuth::carrierUser()->carrier_id;
                                $companyFeeLog->created_at = Carbon::now()->subSeconds(1);
                                $companyFeeLog->amount < 0 && $companyFeeLog->save();
                            }
                            $agentUnderTakeLog->amount = $agentUnderTakeAmount > $conf->deposit_preferential_undertake_max ? $conf->deposit_preferential_undertake_max : $agentUnderTakeAmount;
                            $agentUnderTakeLog->agent_id = $playerDepositPayLog->player->agent->id;
                            $agentUnderTakeLog->carrier_id = $playerDepositPayLog->player->carrier_id;
                            $agentUnderTakeLog->undertaken_type = AgentBearUndertakenLog::UNDERTAKEN_TYPE_DEPOSIT_BENEFIT;
                            $agentUnderTakeLog->amount > 0 && $agentUnderTakeLog->save();
                        }else{
                            $companyFeeLog = new CarrierQuotaConsumptionLog();
                            $companyFeeLog->amount = -$depositBenefit;
                            $companyFeeLog->related_pay_channel = $playerDepositPayLog->carrier_pay_channel;
                            $carrierPayChannel->balance -= $depositBenefit;
                            $companyFeeLog->pay_channel_remain_amount = $carrierPayChannel->balance;
                            if($carrierPayChannel->balance < 0){
                                throw new \Exception('该卡不足以承担这笔存款的存款优惠');
                            }
                            $companyFeeLog->consumption_source = '会员存款优惠';
                            $companyFeeLog->carrier_id = \WinwinAuth::carrierUser()->carrier_id;
                            $companyFeeLog->created_at = Carbon::now()->subSeconds(1);
                            $companyFeeLog->amount < 0 && $companyFeeLog->save();
                        }
                        $playerAccountLog = new PlayerAccountLog();
                        $playerAccountLog->amount = $depositBenefit;
                        $playerAccountLog->carrier_id =  $playerDepositPayLog->player->carrier_id;
                        $playerAccountLog->player_id  = $playerDepositPayLog->player->player_id;
                        $playerAccountLog->fund_type = PlayerAccountLog::FUND_TYPE_DEPOSIT_BENEFIT;
                        $playerAccountLog->fund_source = '存款优惠';
                        $playerAccountLog->operator_reviewer_id = \WinwinAuth::carrierUser()->id;
                        $playerAccountLog->save();
                    }
                    $carrierPayChannel->balance += $playerDepositPayLog->finally_amount;
                    $playerDepositPayLog->player->main_account_amount += $playerDepositPayLog->finally_amount;
                    $playerAccountLog = new PlayerAccountLog();
                    $playerAccountLog->amount = $playerDepositPayLog->amount;
                    $playerAccountLog->carrier_id =  $playerDepositPayLog->player->carrier_id;
                    $playerAccountLog->player_id  = $playerDepositPayLog->player->player_id;
                    $playerAccountLog->fund_type = PlayerAccountLog::FUND_TYPE_DEPOSIT;
                    $payChannelType = $carrierPayChannel->payChannel->payChannelType;//->isCompanyPay();
                    if($payChannelType->isCompanyPay()){
                        $playerAccountLog->fund_source = '公司入款';
                    }else if($payChannelType->isThirdPartPay()){
                        $playerAccountLog->fund_source = '在线支付';
                    }else{
                        $playerAccountLog->fund_source = '点卡充值';
                    }
                    $playerAccountLog->operator_reviewer_id = \WinwinAuth::carrierUser()->id;
                    $playerAccountLog->save();
                    //运营商资金流水
                    $companyDepositLog = new CarrierQuotaConsumptionLog();
                    $companyDepositLog->amount = $playerDepositPayLog->finally_amount;
                    $companyDepositLog->related_pay_channel = $playerDepositPayLog->carrier_pay_channel;
                    $companyDepositLog->pay_channel_remain_amount = $carrierPayChannel->balance;
                    $companyDepositLog->consumption_source = '会员存款';
                    $companyDepositLog->carrier_id = \WinwinAuth::carrierUser()->carrier_id;
                    $companyDepositLog->amount > 0 && $companyDepositLog->save();

                    //取款流水限制
                    $withdrawFlowLimit = new PlayerWithdrawFlowLimitLog();
                    $withdrawFlowLimit->carrier_id = \WinwinAuth::carrierUser()->carrier_id;
                    if($playerDepositPayLog->carrier_activity_id){
                        $withdrawFlowLimit->related_activity = $playerDepositPayLog->carrier_activity_id;
                    }else{
                        $withdrawFlowLimit->limit_amount = $playerDepositPayLog->finally_amount;
                    }
                    $withdrawFlowLimit->limit_type = PlayerWithdrawFlowLimitLog::LIMIT_TYPE_PLAYER_DEPOSIT;
                    $withdrawFlowLimit->player_account_log = $playerAccountLog->log_id;
                    $withdrawFlowLimit->operator_id = \WinwinAuth::carrierUser()->id;
                    $withdrawFlowLimit->player_id = $playerDepositPayLog->player->player_id;
                    $withdrawFlowLimit->limit_amount > 0 && $withdrawFlowLimit->save();
                    $playerDepositPayLog->player->update();
                    $playerDepositPayLog->operate_time = Carbon::now();
                    $playerDepositPayLog->review_user_id = \WinwinAuth::carrierUser()->id;
                    $carrierPayChannel->update();
                    $playerDepositPayLog->update();
                });
                //查找是否有自动参与的优惠活动
                dispatch(new JudgePlayerHasAutoJoinActivity($playerDepositPayLog->player,$playerDepositPayLog->ip));
                if($playerDepositPayLog->status == PlayerDepositPayLog::ORDER_STATUS_PAY_SUCCEED){
                    //玩家升级队列处理
                    dispatch(new PlayerUpgradeLevelHandle(PlayerInfoCacheHelper::getPlayerCacheInfoById($playerDepositPayLog->player_id)));
                    //审核活动
                    if($playerDepositPayLog->carrier_activity_id) {
                        $activity = CarrierActivity::findOrFail($playerDepositPayLog->carrier_activity_id);
                        $carrierActivityAudit = new CarrierActivityAudit();
                        $carrierActivityAudit->act_id = $activity->id;
                        $carrierActivityAudit->carrier_id = $playerDepositPayLog->carrier_id;
                        $carrierActivityAudit->player_id = $playerDepositPayLog->player_id;
                        $carrierActivityAudit->ip = $playerDepositPayLog->ip;
                        try {
                            $activity->checkUserCanApplyActivity($playerDepositPayLog->player_id, $playerDepositPayLog->ip);
                            $carrierActivityAudit->status = CarrierActivityAudit::STATUS_AUDIT;
                        }catch(CarrierRuntimeException $e){
                            $carrierActivityAudit->remark = $e->getMessage();
                            $carrierActivityAudit->status = CarrierActivityAudit::STATUS_REFUSE;
                        }catch (\Exception $e){
                            throw $e;
                        }
                        $carrierActivityAudit->save();
                    }
                }
                return $this->sendSuccessResponse();
            }catch (\Exception $e){
                return $this->sendErrorResponse($e->getMessage());
            }
        }else{
            $playerDepositPayLog->status = PlayerDepositPayLog::ORDER_STATUS_SERVER_REVIEW_NO_PASSED;
            $playerDepositPayLog->operate_time = Carbon::now();
            $playerDepositPayLog->review_user_id = \WinwinAuth::carrierUser()->id;
            $playerDepositPayLog->update();
            return $this->sendSuccessResponse();
        }

    }

    /**
     * Show the form for editing the specified PlayerDepositPayLog.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $playerDepositPayLog = $this->playerDepositPayLogRepository->findWithoutFail($id);

        if (empty($playerDepositPayLog)) {
            return $this->sendNotFoundResponse();
        }

        return view('Carrier.player_deposit_pay_review_logs.edit')->with('playerDepositPayLog', $playerDepositPayLog);
    }

    /**
     * Update the specified PlayerDepositPayLog in storage.
     *
     * @param  int              $id
     * @param UpdatePlayerDepositPayLogRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePlayerDepositPayLogRequest $request)
    {
        $playerDepositPayLog = $this->playerDepositPayLogRepository->findWithoutFail($id);
        if (empty($playerDepositPayLog)) {
            return $this->sendNotFoundResponse();
        }

        $this->playerDepositPayLogRepository->update($request->all(), $id);
        return redirect(route('playerDepositPayLogs.index'));
    }

    /**
     * Remove the specified PlayerDepositPayLog from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $playerDepositPayLog = $this->playerDepositPayLogRepository->findWithoutFail($id);

        if (empty($playerDepositPayLog)) {
            Flash::error('Player Deposit Pay Log not found');

            return redirect(route('playerDepositPayLogs.index'));
        }

        $this->playerDepositPayLogRepository->delete($id);

        Flash::success('Player Deposit Pay Log deleted successfully.');

        return redirect(route('playerDepositPayLogs.index'));
    }
}
