<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\Log\PlayerRebateFinancialFlow;
use App\Services\PassPlayerRebateFinancialFlowService;

class PlayerRebateFinancialFlowController extends AppBaseController
{

    /**
     * 实时洗码
     * @return \View
     */
    public function rebateFinancialFlow(Request $request)
    {
        $type = $request->get('type', '');
        $perPage = $request->get('perPage', 10);
        $playerRebateFinancialFlow = PlayerRebateFinancialFlow::with(['gamePlat','player.playerLevel.rebateFinancialFlow'])
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);
        //可结算总金额
        $settleAmountTotal = PlayerRebateFinancialFlow::unsettled()->sum('rebate_financial_flow_amount');
        if($request->ajax()){
            if($type){
                return \WTemplate::rebateFinancialFlowList()->with('playerRebateFinancialFlow', $playerRebateFinancialFlow);
            }
            return \WTemplate::rebateFinancialFlowRecord()->with(['playerRebateFinancialFlow'=> $playerRebateFinancialFlow, 'settleAmountTotal'=>$settleAmountTotal]);
        }
    }

    /**
     * 结算
     * @param Request $request
     * @return mixed
     */
    public function settleMoney(Request $request){
        $playerRebateFinanceFlowId = $request->get('playerRebateFinanceFlowId');

        if(isset($playerRebateFinanceFlowId) && !$playerRebateFinanceFlowId){
            return $this->sendErrorResponse('参数异常');
        }

        try{
            if($playerRebateFinanceFlowId){
                $rebateFinancialFlowLog = PlayerRebateFinancialFlow::unsettledInIds((array)$playerRebateFinanceFlowId)->with('player.agent.agentLevel')->get();
            }else{
                $rebateFinancialFlowLog = PlayerRebateFinancialFlow::all();
            }

            $handleService = new PassPlayerRebateFinancialFlowService($rebateFinancialFlowLog);
            $handleService->handle();

            //总可结算金额
            $settleAmountTotal = PlayerRebateFinancialFlow::unsettled()->sum('rebate_financial_flow_amount');

        }catch(\Exception $e){
            return $this->sendErrorResponse('结算失败');
        }

        return $this->sendResponse($settleAmountTotal,'结算成功');

    }
}
