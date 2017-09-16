<?php

namespace App\Http\Controllers\Agent;
use App\Http\Requests\Agent;
use App\Repositories\Agent\AgentUserRepository;
use App\DataTables\Agent\AgentPlayerDataTable;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class AgentPlayerController extends AppBaseController
{
    /** @var  AgentUserRepository */
    private $agentUserRepository;

    public function __construct(AgentUserRepository $agentUserRepo)
    {
        $this->agentUserRepository = $agentUserRepo;
    }

    /**
     * Display a listing of the CarrierAgentUser.
     *
     * @param AgentPlayerDataTable $agentPlayerDataTable
     * @return Response
     */
    public function index(AgentPlayerDataTable $agentPlayerDataTable)
    {
        return $agentPlayerDataTable->render('Agent.agent_player.index');
    }

}
