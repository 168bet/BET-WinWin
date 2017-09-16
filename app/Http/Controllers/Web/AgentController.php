<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\AgentAccountException;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Web\AgentLoginRequest;
use App\Http\Requests\Web\CreateCarrierAgentUserRequest;
use App\Models\CarrierAgentDomain;
use App\Models\CarrierAgentUser;
use App\Repositories\Carrier\CarrierAgentUserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//代理前台控制器
class AgentController extends AppBaseController
{
    //首页
    public function index(){
        return view('Web.agents.home');
    }

    //合营模式
    public function pattern(){
        return view('Web.agents.pattern');
    }

    //佣金政策
    public function policy(){
        return view('Web.agents.policy');
    }

    //合营协议
    public function protocol(){
        return view('Web.agents.protocol');
    }

    //联系我们
    public function connectUs(){
        return view('Web.agents.connectUs');
    }

    /**
     * 代理类型二级联动数据
     */
    public function dataAgentLevel(Request $request)
    {
        $data['type']=$request->get('type');
        $data['carrier_id'] = \WinwinAuth::currentWebCarrier()->id;
        $classes= \App\Models\CarrierAgentLevel::where($data)->get();
        echo json_encode($classes);
    }

    /**
     * 注册界面
     * @param $request
     * @return \View
     */
    public function registerPage()
    {
        //获得代理类型下的所有代理
        $carrierAgentLevelName = \App\Models\CarrierAgentLevel::where('type',1)->get();

        $agentRegisterConf = \WinwinAuth::currentWebCarrier()->dashLoginConf;
        //判断运营商是否禁止注册
        if($agentRegisterConf->is_allow_agent_register == 0) {
            return view('Web.agents.banRegister');
        }

        return view('Web.agents.register')->with(['agentRegisterConf'=>$agentRegisterConf,'carrierAgentLevelName'=>$carrierAgentLevelName]);
    }

    /**
     * 验证码
     * @return \Response
     */
    public function captcha(){
        return  $this->sendResponse(\Captcha::img());
    }

    /**
     * 注册处理
     * @param CreatePlayer
     * @return \Response
     */
    //CreatePlayerRequest $request
    public function register(CreateCarrierAgentUserRequest $request,CarrierAgentUserRepository $agentUserRepository){

        //判断验证码是否正确
        if(!\Captcha::check($request->get('refercode'))){
            return $this->sendErrorResponse(['field'=>'refercode', 'message'=>'验证码输入错误'],403);
        }

        $input = $request->all();

        //获取当前网址,判断是否为代理域名,通过代理域名获得父ID
        $webUrl = $request->header('host');
        $agentDomain = CarrierAgentDomain::where('website',$webUrl)->first();

        if ($agentDomain){
            $input['parent_id'] = $agentDomain->agent_id;
        }elseif($request->get('promotion_code')){
            //判断是否有推荐码,是则根据推荐码获得推荐会员ID
            $input['parent_id'] = CarrierAgentUser::where('promotion_code',$request->get('promotion_code'))->value('id');
        }else{
            $agent = CarrierAgentUser::where(['carrier_id'=>\WinwinAuth::currentWebCarrier()->id,'is_default'=>1])->first();
            $input['agent_id'] = $agent ? $agent->id : null;
        }

        //运营商ID
        $input['carrier_id'] = \WinwinAuth::currentWebCarrier()->id;

        //生成随机邀请码
        $input['promotion_code'] = CarrierAgentUser::generateReferralCode();

        //默认取款密码000000
        $input['pay_password'] = bcrypt('000000');

        $input['password'] = bcrypt($request->get('password'));

        //判断是否是推荐用户开户
        if($request->get('parent_id')){
            $input['parent_id'] = $request->get('parent_id');
            $agent = $agentUserRepository->create($input);
            if ($agent) {
                return $this->sendSuccessResponse();
            } else {
                return $this->sendErrorResponse('注册失败', 403);
            }
        }

        try{
            $agent = $agentUserRepository->create($input);
            \WinwinAuth::agentAuth()->loginUsingId($agent->id);
            return $this->sendSuccessResponse();
        }catch(\Exception $e){
            return $this->sendErrorResponse('注册失败', 403);
        }

    }

    /**
     * 登陆处理
     */
    public function login(AgentLoginRequest $request)
    {
//        if(!\Captcha::check($request->get('refercode'))){
//            return $this->sendErrorResponse(['fields'=>'refercode', 'message'=>'验证码输入错误'],403);
//        }
        $username = $request->get('username');
       // $agent = $this->AgentLoginRequest->findWhere(['username' => $username], ['*'])->first();
        $agentUser = CarrierAgentUser::where('username', $request->get('username'))->with('agentLoginConf')->first();
        if($agentUser) {
            try {
                if ($agentUser->isActive()){
                    if (\Hash::check($request->get('password'), $agentUser->password) == true){
                        \WinwinAuth::agentAuth()->loginUsingId($agentUser->id);
                        return $this->sendSuccessResponse();
                    }else{
                        return $this->sendErrorResponse('账户或密码错误', 403);
                    }
                }
            }catch(AgentAccountException $e){
                return $this->sendErrorResponse($e->getMessage(), 403);
            }
        }else {
            return $this->sendErrorResponse('账户错误或不存在', 404);
        }
    }

}
