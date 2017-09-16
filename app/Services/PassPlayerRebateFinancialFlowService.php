<?php
/**
 * Created by PhpStorm.
 * User: wugang
 * Date: 2017/4/13
 * Time: 下午8:40
 */

namespace App\Services;
use App\Models\Log\AgentBearUndertakenLog;
use App\Models\Log\CarrierQuotaConsumptionLog;
use App\Models\Log\PlayerAccountLog;
use App\Models\Log\PlayerRebateFinancialFlow;
use App\Models\Log\PlayerWithdrawFlowLimitLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;


class PassPlayerRebateFinancialFlowService
{

    /**
     * @var Collection
     */
    private $rebateFinancialFlowLogs;

    public function __construct(Collection $rebateFinancialFlowLogs)
    {
        $this->rebateFinancialFlowLogs = $rebateFinancialFlowLogs;
    }


    public function handle()
    {
        try{
            \DB::transaction(function (){
                $this->rebateFinancialFlowLogs->each(function (PlayerRebateFinancialFlow $log){
                    $log->is_already_settled = true;
                    $log->settled_at = Carbon::now();
                    //检测该会员的代理商是否有洗码承担比例,如果代理完全承担洗码成本,那么运营商不需要扣除相应的额度;
                    $agent = $log->player->agent;
                    $agentLevel = $agent->agentLevel;

                    //代理商承担成本
                    $agentUnderTakeAmount = 0;
                    //运营商承担成本
                    $carrierUnderTakeAmount = $log->rebate_financial_flow_amount;
                    //代理商承担最大限额
                    $agentUnderTakeMaxAmount = 0;
                    //代理商是否需要承担
                    $agentNeedUnderTake = false;
                    //只有佣金代理和占成代理才有洗码承担
                    if($agent->isCommissionAgent()){
                        $commissionAgentConf = $agentLevel->commissionAgentConf;
                        //计算代理商应该承担的洗码数据
                        $agentUnderTakeAmount = $log->rebate_financial_flow_amount * $commissionAgentConf->rebate_financial_flow_undertake_ratio * 0.01;
                        $carrierUnderTakeAmount = $log->rebate_financial_flow_amount - $agentUnderTakeAmount;
                        $agentUnderTakeMaxAmount = $commissionAgentConf->rebate_financial_flow_undertake_max;
                        $agentNeedUnderTake = true;
                    }else if ($agent->isCostTakenAgent()){
                        $costTakeAgentConf = $agentLevel->costTakeAgentConf;
                        //计算代理商应该承担的洗码数据
                        $agentUnderTakeAmount = $log->rebate_financial_flow_amount * $costTakeAgentConf->rebate_financial_flow_undertake_ratio * 0.01;
                        $carrierUnderTakeAmount = $log->rebate_financial_flow_amount - $agentUnderTakeAmount;
                        $agentUnderTakeMaxAmount = $costTakeAgentConf->rebate_financial_flow_undertake_max;
                        $agentNeedUnderTake = true;
                    }
                    //如果代理需要承担成本
                    $carrierQuotaLog = null;
                    if($agentNeedUnderTake){
                        $agentUnderTakeLog = new AgentBearUndertakenLog();
                        $agentUnderTakeLog->agent_id = $agent->id;
                        $agentUnderTakeLog->carrier_id = $agent->carrier_id;
                        $agentUnderTakeLog->undertaken_type = AgentBearUndertakenLog::UNDERTAKEN_TYPE_BET_FINANCIAL_FLOW;
                        //如果有最大的承担配置, 那么需要获取代理已经承担的洗码数据判断是否超过最大承担限额;
                        if($agentUnderTakeMaxAmount == 0){
                            //如果没有上限,那么需要将该成本计入代理的承担表中;
                            $agentUnderTakeLog->amount = $agentUnderTakeAmount;
                        }else{
                            //当前代理商已经承受的洗码成本
                            $agentUnSettledRebateFinancialFlowAmount = $agent->unSettledRebateFinancialFlowAmount();
                            //如果已经承受的成本大于等于最大承受额, 那么则由运营商承担成本
                            if($agentUnSettledRebateFinancialFlowAmount >= $agentUnderTakeMaxAmount){
                                $carrierQuotaLog = new CarrierQuotaConsumptionLog();
                                $carrierQuotaLog->carrier_id = $agent->carrier_id;
                                $carrierQuotaLog->amount = $agentUnderTakeAmount + $carrierUnderTakeAmount;
                                $carrierQuotaLog->consumption_source = '玩家洗码成本';
                                $log->player->carrier->checkRemainQuotaEnough($agentUnderTakeAmount);
                                $agentUnderTakeLog = null;
                            }else{
                                //如果小于最大承受额, 那么算差额是否大于0 ,如果大于0 那么多余的还是由运营商承担;
                                $remainFlowUnderTakeAmount = $agentUnSettledRebateFinancialFlowAmount + $agentUnderTakeAmount - $agentUnderTakeMaxAmount;
                                if($remainFlowUnderTakeAmount > 0){
                                    $carrierQuotaLog = new CarrierQuotaConsumptionLog();
                                    $carrierQuotaLog->carrier_id = $agent->carrier_id;
                                    $carrierQuotaLog->amount = $remainFlowUnderTakeAmount + $carrierUnderTakeAmount;
                                    $log->player->carrier->checkRemainQuotaEnough($remainFlowUnderTakeAmount);
                                    $carrierQuotaLog->consumption_source = '由于代理洗码超过最大限额,部分玩家洗码成本由运营商承担';
                                }
                                $agentUnderTakeLog->amount = $remainFlowUnderTakeAmount > 0 ? $agentUnderTakeMaxAmount - $agentUnSettledRebateFinancialFlowAmount : $agentUnderTakeAmount;
                            }
                        }
                        $agentUnderTakeLog && $agentUnderTakeLog->amount > 0 && $agentUnderTakeLog->save();
                    }
                    $carrierQuotaLog && $carrierQuotaLog->save();
//                    if($carrierQuotaLog){
//                        $log->player->carrier->remain_quota -= $carrierQuotaLog->amount;
//                        $log->player->carrier->update();
//                    }
                    $log->update();
                    //玩家资金记录新增;
                    $playerAccountLog = new PlayerAccountLog();
                    $playerAccountLog->amount = $log->rebate_financial_flow_amount;
                    $playerAccountLog->carrier_id = $agent->carrier_id;
                    $playerAccountLog->player_id = $log->player_id;
                    $playerAccountLog->fund_source = '玩家洗码';
                    $playerAccountLog->remark = !\WinwinAuth::carrierUser() ? '系统自动结算' : '玩家自助领取';
                    $playerAccountLog->operator_reviewer_id = \WinwinAuth::carrierUser() ? \WinwinAuth::carrierUser()->id : null;
                    $playerAccountLog->fund_type = PlayerAccountLog::FUND_TYPE_FINANCIAL_FLOW;
                    $playerAccountLog->main_game_plat_id = $log->gamePlat->main_game_plat_id;
                    $playerAccountLog->save();

                    //新增玩家取款限制, 默认1倍流水限制;
                    $playerWithdrawFlowLimitLog = new PlayerWithdrawFlowLimitLog();
                    $playerWithdrawFlowLimitLog->carrier_id = $agent->carrier_id;
                    $playerWithdrawFlowLimitLog->player_account_log = $playerAccountLog->log_id;
                    $playerWithdrawFlowLimitLog->player_id  = $log->player_id;
                    $playerWithdrawFlowLimitLog->limit_amount = $log->rebate_financial_flow_amount;
                    $playerWithdrawFlowLimitLog->limit_type = PlayerWithdrawFlowLimitLog::LIMIT_TYPE_ADJUST_REBATE_FINANCIAL_FLOW;
                    $playerWithdrawFlowLimitLog->limit_amount > 0 && $playerWithdrawFlowLimitLog->save();

                });
            });
        }catch (\Exception $e){
            throw $e;
        }
    }

}