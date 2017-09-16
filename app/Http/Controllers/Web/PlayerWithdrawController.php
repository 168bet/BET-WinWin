<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Web\CreatePlayerBankCardRequest;
use App\Models\Conf\CarrierWithdrawConf;
use App\Models\Log\PlayerWithdrawFlowLimitLog;
use App\Models\Log\PlayerWithdrawLog;
use App\Models\Player;
use App\Models\PlayerBankCard;
use App\Repositories\Carrier\PlayerRepository;
use App\Vendor\GameGateway\Gateway\GameGatewayRunTime;
use App\Vendor\GameGateway\PT\PTGameGateway;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

/**
 * Created by PhpStorm.
 * User: winwin
 * Date: 2017/3/16
 * Time: 下午10:09
 */
class PlayerWithdrawController extends AppBaseController
{

    public function withdrawRecords(Request $request){
        $type = $request->get('type', '');
        $status = $request->get('status', array_keys(PlayerWithdrawLog::statusMeta()));
        $perPage = $request->get('perPage', 10);
        $start_time = $request->get('start_time', Carbon::now()->startOfMonth());
        $end_time = $request->get('end_time', Carbon::now()->endOfMonth());

        if(in_array($status, array_keys(PlayerWithdrawLog::statusMeta())) && !is_array($status)){
            $status = (array)$status;
        }
        if(empty($start_time)){
            $start_time = '2000-01-01';
        }
        if(empty($end_time)){
            $end_time = Carbon::now();
        }

        $withdrawStatus = PlayerWithdrawLog::statusMeta();
        $playerWithdrawLog = PlayerWithdrawLog::where('player_id', \WinwinAuth::memberUser()->player_id)
            ->whereIn('status', $status)
            ->whereBetween('created_at', [$start_time, $end_time])
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);

        if($request->ajax()){
            if($type){
                return \WTemplate::withdrawLists()->with('playerWithdrawLog', $playerWithdrawLog);
            }
            return \WTemplate::withdrawRecords()->with(['playerWithdrawLog'=> $playerWithdrawLog, 'withdrawStatus'=>$withdrawStatus]);
        }
    }

    /**
     * 新增银行卡
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addBankCard(CreatePlayerBankCardRequest $request)
    {
        $input['card_owner_name'] = $request->get('card_owner_name');
        $input['card_account'] = $request->get('card_account');
        $input['card_type'] = $request->get('card_type');
        $input['card_birth_place'] = $request->get('card_birth_place');
        $input['carrier_id'] = \WinwinAuth::currentWebCarrier()->id;
        $input['player_id'] = \WinwinAuth::memberUser()->player_id;

        $playerBankCards = new PlayerBankCard();
        $playerBankCard = $playerBankCards->create($input);
        if ($playerBankCard){
            return $this->sendSuccessResponse();
        } else {
            return $this->sendErrorResponse('新增失败', 404);
        }
    }

    /**
     * 删除银行卡
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function deleteBankCard(Request $request)
    {
        $card_id = $request->get('card_id');

        $playerBankCards = new PlayerBankCard();
        $playerBankCard = $playerBankCards->where('card_id',$card_id)->delete();
        if ($playerBankCard){
            return $this->sendSuccessResponse();
        } else {
            return $this->sendErrorResponse('删除失败', 404);
        }
    }


    /**
     * 取款限额检查
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function withdrawQuotaCheck(Request $request)
    {
        //当前运营商取款设置
        $carrierWithdrawConf = CarrierWithdrawConf::where('carrier_id', \WinwinAuth::currentWebCarrier()->id)->first();

        $withdraw_number = $request->get('withdraw_number');
        if ($withdraw_number){
            if ($withdraw_number < $carrierWithdrawConf->player_once_withdraw_min_sum ){
                return $this->sendErrorResponse('*单次取款最小限额为'.$carrierWithdrawConf->player_once_withdraw_min_sum.'元', 404);
            }elseif (($withdraw_number >\WinwinAuth::memberUser()->main_account_amount) && ($withdraw_number < $carrierWithdrawConf->player_once_withdraw_max_sum)){
                return $this->sendErrorResponse('*账户余额不足', 404);
            }elseif ($withdraw_number > $carrierWithdrawConf->player_once_withdraw_max_sum){
                return $this->sendErrorResponse('*单次取款最大限额为'.$carrierWithdrawConf->player_once_withdraw_max_sum.'元', 404);
            }else{
                return $this->sendResponse([],'*注意：单次最低提款额为'.$carrierWithdrawConf->player_once_withdraw_min_sum.'元,最高'.$carrierWithdrawConf->player_once_withdraw_max_sum.'元');
            }
        }

    }

    /**
     * 取款申请
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function withdrawApply(Request $request)
    {
        $input = $request->all();
        $input['carrier_id'] = \WinwinAuth::currentWebCarrier()->id;
        $input['player_id'] = \WinwinAuth::memberUser()->player_id;

        //生成取款单号
        $input['order_number'] = PlayerWithdrawLog::generateOrderNumber();
        //默认申请状态
        $input['status'] = PlayerWithdrawLog::STATUS_WAITING_REVIEWED;

        $start_time = Carbon::now()->startOfDay();
        $end_time = Carbon::now()->endOfDay();

        //当前运营商取款设置
        $carrierWithdrawConf = CarrierWithdrawConf::where('carrier_id', \WinwinAuth::currentWebCarrier()->id)->first();
        //判断是否允许玩家取款
       if($carrierWithdrawConf->is_allow_player_withdraw != true){
           return $this->sendErrorResponse('系统升级中,有疑问请联系客服',404);
       }

       //判断玩家取款金额是否符合条件

        //判断取款金额是否超过单日限额
        $playWithdrawSum = PlayerWithdrawLog::AccountOut()
            ->whereBetween('created_at',[$start_time,$end_time])
            ->sum('apply_amount');

        if (($input['apply_amount'] >\WinwinAuth::memberUser()->main_account_amount)){
            return $this->sendErrorResponse('账户余额不足', 404);
        }elseif ($input['apply_amount'] < $carrierWithdrawConf->player_once_withdraw_min_sum ){
            return $this->sendErrorResponse('单次取款最小限额为'.$carrierWithdrawConf->player_once_withdraw_min_sum.'元', 404);
        }elseif ($input['apply_amount'] > $carrierWithdrawConf->player_once_withdraw_max_sum){
            return $this->sendErrorResponse('单次取款最大限额为'.$carrierWithdrawConf->player_once_withdraw_max_sum.'元', 404);
        }elseif (($input['apply_amount'] + $playWithdrawSum) > $carrierWithdrawConf->player_day_withdraw_max_sum){
            return $this->sendErrorResponse('单日取款最大限额为'.$carrierWithdrawConf->player_day_withdraw_max_sum.'元',404);
        }


       //判断是否开启流水检查
       if($carrierWithdrawConf->is_check_flow_water_when_withdraw){
            $withDrawFlowRecordCounts = PlayerWithdrawFlowLimitLog::unfinished()->count();
            //dd($withDrawFlowRecords);
            if ($withDrawFlowRecordCounts > 0){
                return $this->sendErrorResponse('流水未完成，不能提款',404);
            }
        }



        //判断当日取款成功次数是否已超出
        $playWithdraw = PlayerWithdrawLog::AccountOut()
            ->whereBetween('created_at',[$start_time,$end_time])
            ->count();
        if (($playWithdraw != false) && ($playWithdraw >= $carrierWithdrawConf->player_day_withdraw_success_limit_count)){
            return $this->sendErrorResponse('当日取款成功次数不能超过'.$carrierWithdrawConf->player_day_withdraw_success_limit_count.'次',404);
        }

        $player = Player::where('player_id',\WinwinAuth::memberUser()->player_id)->first();


        if (\Hash::check($request->get('pay_password'), $player->pay_password) != true){
            return $this->sendErrorResponse('取款密码输入错误', 403);
        }else {
            try {
                \DB::transaction(function () use ($input, $player) {
                    $playerWithdrawLog = PlayerWithdrawLog::create($input);
                    if ($playerWithdrawLog) {
                        $player->frozen_main_account_amount = $input['apply_amount'];
                        $player->main_account_amount = $player->main_account_amount - $input['apply_amount'];
                        $player->save();
                    }
                });
                return $this->sendSuccessResponse();
            } catch (\Exception $e) {
                return $this->sendErrorResponse($e->getMessage());
            }
        }
    }


}