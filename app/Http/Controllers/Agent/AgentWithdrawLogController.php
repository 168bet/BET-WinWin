<?php

namespace App\Http\Controllers\Agent;
use App\Http\Requests\Agent;
use App\DataTables\Agent\AgentWithdrawLogDataTable;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;
use Illuminate\Http\Request;

class AgentWithdrawLogController extends AppBaseController
{

    public function index(AgentWithdrawLogDataTable $agentWithdrawLogDataTable)
    {
        return $agentWithdrawLogDataTable->render('Agent.agent_withdraw_log.index');
    }

}
