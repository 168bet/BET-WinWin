<?php

namespace App\Http\Controllers\Agent;
use App\Http\Requests\Agent;
use App\Repositories\Agent\AgentUserRepository;
use App\Repositories\Carrier\CarrierAgentUserRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Response;
use App\Models\CarrierAgentUser;
use App\Models\CarrierAgentLevel;

class AgentAccountCenterController extends AppBaseController
{
    /** @var  CarrierAgentUserRepository */
    private $agentUserRepository;

    public function __construct(AgentUserRepository $agentUserRepo)
    {
        $this->agentUserRepository = $agentUserRepo;
    }

    /**
     * Display a listing of the CarrierAgentUser.
     *
     * @param CarrierAgentUserDataTable $carrierAgentUserDataTable
     * @return Response
     */
    public function index()
    {
        //代理用户个人信息
        $agentAccountCenter = CarrierAgentUser::where(['id'=>\WinwinAuth::agentUser()->id])->with('agentBankCard.bankType')->first();
        //dd($agentAccountCenter->agentBankCard);
        if (empty($agentAccountCenter)) {
            return $this->renderNotFoundPage();
        }
        if($agentAccountCenter['agent_level_id'])
        {
            //运营商代理等级信息
            $carrierAgentLevel = CarrierAgentLevel::where(['id'=>$agentAccountCenter['agent_level_id']])->first();
            //代理名称
            $carrierAgentLevelType = CarrierAgentLevel::typeMeta()[$carrierAgentLevel->type];
        }else{
            $carrierAgentLevel = null;
            $carrierAgentLevelType = null;
        }


        return view('Agent.agent_account_center.index')->with(['agentAccountCenter'=>$agentAccountCenter,'carrierAgentLevel'=>$carrierAgentLevel,'carrierAgentLevelType'=>$carrierAgentLevelType]);
    }

    /*
     * 更新代理信息
     */
    public function agentInformationUpdate(Agent\UpdateCarrierAgentUserRequest $request,CarrierAgentUserRepository $agentUserRepository){
       $input = $request->all();
        $id = \WinwinAuth::agentUser()->id ;
        $agentUser = CarrierAgentUser::where('id',$id)->first();
      if (empty($agentUser)){
          return $this->renderNotFoundPage();
      }

      $result = $agentUserRepository->update($input,$id);
      if ($result){
          return $this->sendSuccessResponse(route('agentAccountCenters.index'));
      }else{
          return $this->sendErrorResponse('信息保存失败', 404);
      }

    }
}
