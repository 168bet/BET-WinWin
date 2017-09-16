<?php

namespace App\Http\Controllers\Agent;

use App\Http\Requests\Carrier\CreateCarrierAgentSettleLogRequest;
use App\Http\Requests\Carrier\UpdateCarrierAgentSettleLogRequest;
use App\Repositories\Carrier\CarrierAgentSettleLogRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\DataTables\Agent\AgentSettleReportDataTable;
use App\Models\Log\CarrierAgentSettleLog;
use App\Models\Log\CarrierAgentSettlePeriodsLog;
use App\Models\Def\GamePlat;
use App\Models\Log\AgentRebateFinancialFlow;

class AgentSettleReportController extends AppBaseController
{
    /** @var  CarrierAgentCommissionSettleLogRepository */
    private $carrierAgentSettleLogRepository;

    public function __construct(CarrierAgentSettleLogRepository $carrierAgentSettleLogRepo)
    {
        $this->carrierAgentSettleLogRepository = $carrierAgentSettleLogRepo;
    }

    /**
     * Display a listing of the CarrierAgentCommissionSettleLog.
     *
     * @param Request $request
     * @return Response
     */
    public function index(AgentSettleReportDataTable $agentSettleReportDataTable)
    {
        return $agentSettleReportDataTable->render('Agent.agent_settle_reports.index');
    }

    /**
     * 查看详情
     * @param type $id
     */
    public function details($id)
    {
        $settleLog = CarrierAgentSettleLog::where(['id'=>$id])->first();
        $periodsLog = CarrierAgentSettlePeriodsLog::where(['id'=>$settleLog->periods_id])->first();
        $agentRebateFinancialFlow = AgentRebateFinancialFlow::whereBetween('created_at', [$periodsLog->start_time, $periodsLog->end_time])->where(['agent_id'=>$periodsLog->agent_id])->get();
        $gamePlatIds = null;
        foreach ($agentRebateFinancialFlow as $key => $value) {
            $gamePlatIds[] = $value['game_plat_id'];
        }
        if($gamePlatIds == null)
        {
            $gamePlat = array();
        }else{
            $gamePlat = GamePlat::whereIn('game_plat_id',$gamePlatIds)->get();
            foreach ($gamePlat as $key => $value) {
                $gamePlat[$key]['agentRebateFinancialFlow'] = AgentRebateFinancialFlow::whereBetween('created_at', [$periodsLog->start_time, $periodsLog->end_time])->where(['game_plat_id'=>$value->game_plat_id])->get();
            }
        }
        return view('Agent.agent_settle_reports.details')->with(['gamePlat'=>$gamePlat,
           
            ]);;
    }
    
}
