<?php

namespace App\Http\Controllers\Carrier;

use App\Http\Requests\Carrier\CreateCarrierAgentSettleLogRequest;
use App\Http\Requests\Carrier\UpdateCarrierAgentSettleLogRequest;
use App\Repositories\Carrier\CarrierAgentSettleLogRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\DataTables\Carrier\CarrierAgentSettleLogDataTable;
use App\Models\CarrierAgentUser;
use App\Models\Log\PlayerBetFlowLog;
use App\Models\Log\AgentBearUndertakenLog;
use Illuminate\Support\Facades\DB;
use App\Models\Log\CarrierAgentSettleLog;
use App\Models\Log\CarrierAgentSettlePeriodsLog;
use App\Models\CarrierAgentLevel;
use App\Models\Log\PlayerDepositPayLog;
use App\Models\Player;
use App\Models\Log\AgentRebateFinancialFlow;
use Carbon\Carbon;

class CarrierAgentSettleLogController extends AppBaseController
{
    /** @var  CarrierAgentSettleLogRepository */
    private $carrierAgentSettleLogRepository;

    public function __construct(CarrierAgentSettleLogRepository $carrierAgentSettleLogRepo)
    {
        $this->carrierAgentSettleLogRepository = $carrierAgentSettleLogRepo;
    }

    /**
     * Display a listing of the CarrierAgentSettleLog.
     *
     * @param Request $request
     * @return Response
     */
    public function index(CarrierAgentSettleLogDataTable $carrierAgentSettleLogDataTable)
    {
        return $carrierAgentSettleLogDataTable->render('Carrier.carrier_agent_commission_settle_logs.index');
    }

    /**
     * Show the form for creating a new CarrierAgentSettleLog.
     *log_player_bet_flow
     * @return Response
     */
    public function create()
    {
        $periods  = date("Y-m",mktime(0,0,0,date("m") ,0,date("Y")));//月份
        return view('Carrier.carrier_agent_commission_settle_logs.create')->with(['periods'=>$periods]);
    }

    /**
     * Store a newly created CarrierAgentSettleLog in storage.
     *
     * @param CreateCarrierAgentSettleLogRequest $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        if($request->get('type') == CarrierAgentSettleLog::LAST_WEEK)
        {
            $time = time("Y-m-d H:i:s") - 86400 * 7;
            $start_time = date('Y-m-d H:i:s',mktime( 0,0, 0, date('m',$time) ,date('d',$time) - date('N',$time) + 1 ,date( 'Y',$time )));//上周
            $end_time = date('Y-m-d H:i:s',mktime( 23,59,59, date('m',$time) ,date('d',$time) - date('N',$time) + 7 , date('Y',$time)));//上周
            $periods  = "".$start_time."至".$end_time."";
            
            $last_start_time  = date('Y-m-d H:i:s',mktime( 0,0, 0, date('m',$time) ,date('d',$time) - date('N',$time) + (-6) ,date( 'Y',$time )));//上上个月时间
            $last_end_time  = date('Y-m-d H:i:s',mktime( 23,59,59, date('m',$time) ,date('d',$time) - date('N',$time) + 0 , date('Y',$time)));//上上个月时间
            $last_time = "".$last_start_time."至".$last_end_time."";
        }else if($request->get('type') == CarrierAgentSettleLog::LAST_MONTH)
        {
            $start_time  = date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m")-1,1,date("Y")));//上个月的月初时间
            $end_time  = date("Y-m-d H:i:s",mktime(23,59,59,date("m") ,0,date("Y")));//上个月的月末时间
            $periods  = date("Y-m",mktime(0,0,0,date("m") ,0,date("Y")));
            
            $last_time  = date("Y-m",mktime(0, 0 , 0,date("m")-2,1,date("Y")));//上上个月时间
        }else if($request->get('type') == CarrierAgentSettleLog::FIRST_HALF_MONTH)
        {
            $start_time  = date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m")-1,1,date("Y")));//上个月的月初时间
            $end_time  = date("Y-m-d H:i:s",mktime(23,59,59,date("m") ,0,date("Y")));//上个月的月末时间
            $periods  = date("Y-m",mktime(0,0,0,date("m") ,0,date("Y")));
            
            $last_time  = date("Y-m",mktime(0, 0 , 0,date("m")-2,1,date("Y")));//上上个月时间
        }
        
        $carrierCommissionSettlePeriodsLog = CarrierAgentSettlePeriodsLog::where('periods',$periods)->where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->first();
        
        //洗码结算
        $agentRebateFinancialFlow = AgentRebateFinancialFlow::whereBetween('created_at', [$start_time, $end_time])->where(['is_settled'=>0])->where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->get();
        $agentIds = null;
        foreach ($agentRebateFinancialFlow as $key => $value) {
            $agentIds[] = $value['agent_id'];
        }
        if($agentIds == null)
        {
            return $this->sendErrorResponse('暂无结算数据。');
        }
        
        $agentUser = CarrierAgentUser::whereIn('id',$agentIds)->with(['players','agentLevel.commissionAgentConf'])->where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->get();
        foreach ($agentUser as $key => $value) {
            //累加上月
            $lastPeriods = CarrierAgentSettlePeriodsLog::where(['periods'=>$last_time,'agent_id'=>$value->id])->where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->with(['commissionSettle'])->first();
            if($lastPeriods == null)
            {
                $agentUser[$key]['cumulative_last_month'] = 0.00;
            }else{
                $agentUser[$key]['cumulative_last_month'] = $lastPeriods->commissionSettle->transfer_next_month;//累加上月
            }
            foreach ($value['players'] as $k => $item) {
                $playerIds[] = $item['player_id'];
            }
                //根据存款额和投注额判断是否是有效会员
                //公司输赢 累加值（各个平台的输赢×各个平台抽佣比例）
                $agentUser[$key]['game_plat_win_amount'] = PlayerBetFlowLog::select(DB::raw('SUM(company_win_amount) as company_win_amount'))->whereBetween('created_at', [$start_time, $end_time])->whereIn('player_id',$playerIds)->where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->get();
                
                if($agentUser[$key]['game_plat_win_amount'][0]['company_win_amount'] == null)
                {
                    $agentUser[$key]['game_plat_win_amount'][0]['company_win_amount'] = 0.00;
                }
                
                if($value['agentLevel']['type'] == CarrierAgentLevel::COMMISSION_AGETN)
                {//佣金代理
                    //本期佣金 佣金收入=总输赢×总佣金抽佣比例
                    $agentUser[$key]['this_period_commission'] = $agentUser[$key]['game_plat_win_amount'][0]['company_win_amount'] * ($value['agentLevel']['commissionAgentConf']['commission_ratio'] / 100 );

                }else if($value['agent']['agentLevel']['type'] == CarrierAgentLevel::REBATE_FINANCIAL_FLOW_AGENT)
                {//洗码代理
                    //本期佣金 
                    $agentUser[$key]['this_period_commission'] = $agentUser[$key]['game_plat_win_amount'][0]['company_win_amount'];
                }
                
                //有效投注流水
                $betFlow = count(PlayerBetFlowLog::whereBetween('created_at', [$start_time, $end_time])->whereIn('player_id',$playerIds)->where('bet_flow_available',PlayerBetFlowLog::BET_FLOW_AVAILABLE)->where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->get());
                
                //有效会员投注额
//                    $agentUser[$key]['available_betFlow'] = PlayerBetFlowLog::select(DB::raw('SUM(available_bet_amount) as available_bet_amount'))->whereBetween('created_at', [$start_time, $end_time])->where(['player_id'=>$v1['player_id'],'bet_flow_available'=>PlayerBetFlowLog::BET_FLOW_AVAILABLE])->get();

                //存款额
                $depositPay = PlayerDepositPayLog::whereBetween('created_at', [$start_time, $end_time])->whereIn('player_id',$playerIds)->where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->get();
                foreach ($depositPay as $dep_key => $dep_item) {
                    $dep_plyaerIds[] = $dep_item['player_id'];
                }
                
                $count_depositPay = count(Player::whereIn('player_id',$dep_plyaerIds)->where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->get());
                
                if($betFlow > 0 && $count_depositPay > 0)
                {
                    $agentUser[$key]['available_players'] = count(Player::whereIn('player_id',$playerIds)->where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->get());
                }else{
                    $agentUser[$key]['available_players'] = 0;
                }
                
            if($count_depositPay > $value['agentLevel']['commissionAgentConf']['available_member_count'])
            {
                //有效会员
                $agentUser[$key]['available_member'] = "".$count_depositPay."(已达标)";
            }else{
                //有效会员
                $agentUser[$key]['available_member'] = "".$count_depositPay."(未达标，最少要求".$value['agentLevel']['commissionAgentConf']['available_member_count'].")";
            }

            //成本分摊 存款优惠=累加（代理所有玩家存款优惠）×存款优惠承担比例×总佣金抽佣比例
            //红利=累加（代理所有玩家红利）×红利承担比例×总佣金抽佣比例
            //返水=累加（代理所有玩家返水）×返水承担比例×总佣金抽佣比例
            $bearUndertaken = AgentBearUndertakenLog::select(DB::raw('SUM(amount) as bearUndertaken'))->whereBetween('created_at', [$start_time, $end_time])->where('agent_id',$value['id'])->where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->get();
            $rebateFinancialFlow = AgentRebateFinancialFlow::select(DB::raw('SUM(amount) as rebateFinancialFlow'))->whereBetween('created_at', [$start_time, $end_time])->where('agent_id',$value['id'])->where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->where(['is_settled'=>0])->get();
            $agentUser[$key]['cost_share'] = $rebateFinancialFlow[0]['rebateFinancialFlow'] - $bearUndertaken[0]['bearUndertaken'];
            
            if(empty($carrierCommissionSettlePeriodsLog))
            {
                $settelPeriods= new CarrierAgentSettlePeriodsLog();
                $settelPeriods->carrier_id = \WinwinAuth::carrierUser()->carrier_id;
                $settelPeriods->agent_id = $value['id'];
                $settelPeriods->periods = $periods;
                $settelPeriods->start_time = $start_time;
                $settelPeriods->end_time = $end_time;
                $settelPeriods->save();
                
                $settlePeriodsMaxId = CarrierAgentSettlePeriodsLog::lastSettlePeriodsId();
                $settel = new CarrierAgentSettleLog();
                $settel->carrier_id = \WinwinAuth::carrierUser()->carrier_id;
                $settel->agent_id = $value['id'];
                $settel->periods_id = $settlePeriodsMaxId;
                $settel->available_member_number = $agentUser[$key]['available_member'];//有效会员数(0未达标,最少多少)
                $settel->game_plat_win_amount = $agentUser[$key]['game_plat_win_amount'][0]['company_win_amount'];//公司输赢
                $settel->available_player_bet_amount = 0.00;//有效会员投注额
                $settel->cost_share = $agentUser[$key]['cost_share'];//成本分摊(优惠、红利、洗码)
                $settel->cumulative_last_month = $agentUser[$key]['cumulative_last_month'];//累加上月
                $settel->manual_tuneup = 0.00;//手工调整
                $settel->this_period_commission = $agentUser[$key]['this_period_commission'];//本期佣金
                $settel->actual_payment = 0.00;//实际发放
                $settel->transfer_next_month = 0.00;//转结下月
                $settel->status = 1;//状态
                $settel->save();
                
                $settleMaxId = CarrierAgentSettleLog::lastSettlePeriodsId();
                $data_info['is_settled'] = 1;
                $data_info['log_agent_settled_id'] = $settleMaxId;
                $data_info['settled_at'] = Carbon::now();
                AgentRebateFinancialFlow::whereBetween('created_at', [$start_time, $end_time])->where(['agent_id'=>$value['id']])->update($data_info);
            }else{
                return $this->sendErrorResponse($periods.'已经结算过了。');
            }
        }
        return $this->sendSuccessResponse(route('carrierAgentSettleLogs.index'));
    }
   
    /**
     * 公司输赢报表
     */
    public function gamePlatWinAmount($id)
    {
        $carrierAgentCommissionSettleLog = $this->carrierAgentSettleLogRepository->findWithoutFail($id);
        return view('Carrier.carrier_agent_commission_settle_logs.game_plat_win_amount')->with('carrierAgentCommissionSettleLog',$carrierAgentCommissionSettleLog);
    }
    
    /**
     * 成本分摊
     */
    public function costShare($id)
    {
        $carrierAgentCommissionSettleLog = $this->carrierAgentSettleLogRepository->findWithoutFail($id);
        $carrierCommissionSettlePeriods = CarrierAgentSettlePeriodsLog::where(['id'=>$carrierAgentCommissionSettleLog->periods_id])->where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->first();
        $start_time = $carrierCommissionSettlePeriods->start_time;
        $end_time = $carrierCommissionSettlePeriods->end_time;
        
        $agentUser = CarrierAgentUser::where('id',$carrierAgentCommissionSettleLog->agent_id)->with(['players','agentLevel.commissionAgentConf'])->where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->first();
        //存款优惠比例
        $depositRatio = $agentUser->agentLevel->commissionAgentConf->deposit_preferential_undertake_ratio;
        //红利比例
        $bonusRatio = $agentUser->agentLevel->commissionAgentConf->bonus_undertake_ratio;
        //洗码比例
        $rebateRatio = $agentUser->agentLevel->commissionAgentConf->rebate_financial_flow_undertake_ratio;
        $playerIds = null;
        foreach ($agentUser->players as $key => $value) {
            $playerIds[] = $value['player_id'];
        }
        if($playerIds == null)
        {
            $rebateAmount = sprintf("%.2f", 0);//洗码
            $depositAmount = sprintf("%.2f", 0);//存款优惠
            $bonusAmount = sprintf("%.2f", 0);//红利
        }else{
            //玩家返水记录  洗码总额
            $rebateAmount = AgentRebateFinancialFlow::select(DB::raw('SUM(amount) as costShare'))->whereBetween('created_at', [$start_time, $end_time])->whereIn('id',$playerIds)->where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->get();
            $rebateAmount = sprintf("%.2f", $rebateAmount[0]['costShare']);
            
            if($rebateAmount == null)
            {
                $rebateAmount = sprintf("%.2f", 0);
            }
            $totalRebateAmount = sprintf("%.2f", $rebateAmount * ($rebateRatio / 100));
            
            //优惠金额  优惠总额
            $depositAmount = PlayerDepositPayLog::select(DB::raw('SUM(benefit_amount) as benefitAmount'))->whereBetween('created_at', [$start_time, $end_time])->whereIn('id',$playerIds)->where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->where(['status'=>1])->get();
            $depositAmount = sprintf("%.2f", $depositAmount[0]['benefitAmount']);
            if($depositAmount == null)
            {
                $depositAmount = sprintf("%.2f", 0);
            }
            $totalDepositAmount = sprintf("%.2f", $depositAmount * ($depositRatio / 100));
            
            //红利金额  红利总额
            $bonusAmount = PlayerDepositPayLog::select(DB::raw('SUM(bonus_amount) as bonusAmount'))->whereBetween('created_at', [$start_time, $end_time])->whereIn('id',$playerIds)->where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->where(['status'=>1])->get();
            $bonusAmount = sprintf("%.2f", $bonusAmount[0]['bonusAmount']);
            if($bonusAmount == null)
            {
                $bonusAmount = sprintf("%.2f", 0);
            }
            $totalBonusAmount = sprintf("%.2f", $bonusAmount * ($bonusRatio / 100));
        }
        return view('Carrier.carrier_agent_commission_settle_logs.cost_share')->with(['carrierAgentCommissionSettleLog'=>$carrierAgentCommissionSettleLog,
            'rebateAmount'=>$rebateAmount,
            'depositAmount'=>$depositAmount,
            'bonusAmount'=>$bonusAmount,
            'depositRatio'=>$depositRatio,
            'bonusRatio'=>$bonusRatio,
            'rebateRatio'=>$rebateRatio,
            'totalRebateAmount'=>$totalRebateAmount,
            'totalDepositAmount'=>$totalDepositAmount,
            'totalBonusAmount'=>$totalBonusAmount,
            ]);
    }
    
    /**
     * 手工调整
     */
    public function manualTuneup($id)
    {
        $carrierAgentCommissionSettleLog = $this->carrierAgentSettleLogRepository->findWithoutFail($id);
        return view('Carrier.carrier_agent_commission_settle_logs.manual_tuneup')->with('carrierAgentCommissionSettleLog',$carrierAgentCommissionSettleLog);
    }
    
    /**
     * 保存手工调整
     */
    public function saveManualTuneup($id,Request $request)
    {
        $carrierAgentCommissionSettleLog = $this->carrierAgentSettleLogRepository->findWithoutFail($id);
        if (empty($carrierAgentCommissionSettleLog)) {
           return $this->sendNotFoundResponse();
        }
        $data['manual_tuneup'] = $request->get("manual_tuneup");
        //本期佣金=游戏平台佣金-成本分摊+累加上月+手工调整
        $data['this_period_commission'] = $carrierAgentCommissionSettleLog['game_plat_win_amount'] - $carrierAgentCommissionSettleLog['cost_share'] + $carrierAgentCommissionSettleLog['cumulative_last_month'] + $request->get("manual_tuneup");
        \App\Models\Log\CarrierAgentSettleLog::where(['id'=>$id])->update($data);
        
        if($request->ajax()){
            return self::sendResponse([""],'ok');
        }
        return $this->sendSuccessResponse(route('carrierAgentSettleLogs.index'));
    }
    
    /**
     * 实际发放
     */
    public function actualPayment($id)
    {
        $carrierAgentCommissionSettleLog = $this->carrierAgentSettleLogRepository->findWithoutFail($id);
        return view('Carrier.carrier_agent_commission_settle_logs.actual_payment')->with('carrierAgentCommissionSettleLog',$carrierAgentCommissionSettleLog);
    }
    
    /**
     * 保存实际发放
     */
    public function saveActualPayment($id,Request $request)
    {
        $carrierAgentCommissionSettleLog = $this->carrierAgentSettleLogRepository->findWithoutFail($id);
        if (empty($carrierAgentCommissionSettleLog)) {
           return $this->sendNotFoundResponse();
        }
        $data['actual_payment'] = $request->get("actual_payment");
        //结转下月=本期佣金-实际发放
        $data['transfer_next_month'] = $carrierAgentCommissionSettleLog['this_period_commission'] - $request->get("actual_payment");
        CarrierAgentSettleLog::where(['id'=>$id])->update($data);
        
        if($request->ajax()){
            return self::sendResponse([""],'ok');
        }
        return $this->sendSuccessResponse(route('carrierAgentSettleLogs.index'));
    }
    
    /**
     * 初审
     * @param type $id
     * @return type
     */
    public function theTrial($id)
    {
        $carrierAgentCommissionSettleLog = $this->carrierAgentSettleLogRepository->findWithoutFail($id);
        return view('Carrier.carrier_agent_commission_settle_logs.the_trial')->with('carrierAgentCommissionSettleLog',$carrierAgentCommissionSettleLog);
    }
    
    /**
     * 
     * @param type $id
     * @return type
     */
    public function saveTheTrial($id,Request $request)
    {
        $carrierAgentCommissionSettleLog = $this->carrierAgentSettleLogRepository->findWithoutFail($id);
        if (empty($carrierAgentCommissionSettleLog)) {
           return $this->sendNotFoundResponse();
        }
        $data['remark'] = $request->get("remark");
        $data['status'] = 2;
        CarrierAgentSettleLog::where(['id'=>$id])->update($data);
        if($request->ajax()){
            return self::sendResponse([""],'ok');
        }
        return $this->sendSuccessResponse(route('carrierAgentSettleLogs.index'));
    }
    
    /**
     * 复审
     * @param type $id
     * @return type
     */
    public function reviewTrial($id)
    {
        $carrierAgentCommissionSettleLog = $this->carrierAgentSettleLogRepository->findWithoutFail($id);
        return view('Carrier.carrier_agent_commission_settle_logs.review_trial')->with('carrierAgentCommissionSettleLog',$carrierAgentCommissionSettleLog);
    }
    
    /**
     * 保存复审
     * @param type $id
     * @return type
     */
    public function saveReviewTrial($id,Request $request)
    {
        $carrierAgentCommissionSettleLog = $this->carrierAgentSettleLogRepository->findWithoutFail($id);
        if (empty($carrierAgentCommissionSettleLog)) {
           return $this->sendNotFoundResponse();
        }
        $data['remark'] = $request->get("remark");
        $data['status'] = 3;
        CarrierAgentSettleLog::where(['id'=>$id])->update($data);
        $agentUser = CarrierAgentUser::where(['id'=>$carrierAgentCommissionSettleLog->agent_id])->first();
        $agentUserdata['amount'] = $agentUser['amount'] + $carrierAgentCommissionSettleLog->actual_payment;
        CarrierAgentUser::where(['id'=>$carrierAgentCommissionSettleLog->agent_id])->update($agentUserdata);
        if($request->ajax()){
            return self::sendResponse([""],'ok');
        }
        return $this->sendSuccessResponse(route('carrierAgentSettleLogs.index'));
    }
    
    /**
     * 重新计算代理结算单
     * @return type
     */
    public function reSettlement()
    {
        return view('Carrier.carrier_agent_commission_settle_logs.re_settlement');
    }
    
    /**
     * 保存重新计算代理结算单
     * @return type
     */
    public function saveReSettlement(Request $request)
    {
        $carrierAgentCommissionSettleLog = CarrierAgentSettleLog::where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->where('status','!=',CarrierAgentSettleLog::SET_COMPLETED_STATUS)->get();
        $periodsIds = null;
        foreach ($carrierAgentCommissionSettleLog as $key => $value) {
            $periodsIds[] = $value['periods_id'];
        }
        $agentIds = null;
        foreach ($carrierAgentCommissionSettleLog as $key => $value) {
            $agentIds[] = $value['agent_id'];
        }
        if($periodsIds == null)
        {
            return $this->sendErrorResponse('暂无结算数据。');
        }
        if($agentIds == null)
        {
            return $this->sendErrorResponse('暂无结算数据。');
        }
        $commissionSettlePeriods = CarrierAgentSettlePeriodsLog::whereIn('id',$periodsIds)->get();
        AgentRebateFinancialFlow::where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->whereIn('agent_id',$agentIds)->whereBetween('created_at', [$commissionSettlePeriods[0]['start_time'], $commissionSettlePeriods[0]['end_time']])->where('is_settled','=',1)->update(['is_settled'=>0,'periods_id'=>0]);//
        CarrierAgentSettlePeriodsLog::where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->whereIn('id',$periodsIds)->delete();
        CarrierAgentSettleLog::where(['carrier_id'=>\WinwinAuth::carrierUser()->carrier_id])->where('status','!=',CarrierAgentSettleLog::SET_COMPLETED_STATUS)->delete();
        if($request->ajax()){
            return self::sendResponse([""],'ok');
        }
        return $this->sendSuccessResponse(route('carrierAgentSettleLogs.index'));
    }
    
}
