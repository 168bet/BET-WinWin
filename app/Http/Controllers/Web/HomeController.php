<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\PlayerAccountException;
use App\Helpers\IP\RealIpHelper;
use App\Http\Requests\Web\PlayerLoginRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\CarrierAgentDomain;
use App\Models\CarrierAgentLevel;
use App\Models\CarrierAgentUser;
use App\Models\CarrierPlayerLevel;
use App\Models\Player;
use App\Repositories\Carrier\CarrierAgentDomainRepository;
use App\Repositories\Member\PlayerRepository;
use App\Repositories\Web\PlayerLoginRepository;
use App\Repositories\Web\PlayerLoginLogRepository;
use App\Http\Requests\Web\CreatePlayerRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Conf\PlayerRegisterConf;


class HomeController extends AppBaseController
{
    private $playerLoginRepository;
    public function __construct(PlayerLoginRepository $playerLoginRepository)
    {
        $this->playerLoginRepository = $playerLoginRepository;
    }

    /**
     * 首界面
     * @return \View
     */
    public function index()
    {

        return \WTemplate::homePage();
    }

    /**
     * 登陆处理
     */
    public function login(PlayerLoginRequest $request)
    {
        //TODO  登录失败锁定逻辑未完成  验证码缺少  限频处理
//        if(\App::environment() != 'local'){
//            if(!\Captcha::check($request->get('loginVericode'))){
//                return $this->sendErrorResponse(['fields'=>'loginVericode', 'message'=>'验证码输入错误'],403);
//            }
//        }
        $user_name = $request->get('user_name');
        $player = $this->playerLoginRepository->findWhere(['user_name' => $user_name], ['*'])->first();
        if($player) {
            try {
                $player->checkLocked() && $player->carrier->checkIsAllowUserLogin();
                if (\Hash::check($request->get('password'), $player->password) == true){
                    \WinwinAuth::memberAuth()->loginUsingId($player->player_id);
                    $this->playerLoginRepository->update(['login_at'=>Carbon::now()], $player->player_id);
                    return $this->sendSuccessResponse(route('players.account-security'));
                }else{
                    return $this->sendErrorResponse('账户或密码错误', 403);
                }
            } catch(PlayerAccountException $e){
                return $this->sendErrorResponse($e->getMessage(), 403);
            } catch(\Exception $e){
                \WLog::error('用户登录失败: error'.$e->getMessage());
                return $this->sendErrorResponse('系统错误', 500);
            }
        }else {
            return $this->sendErrorResponse('账户错误或不存在', 404);
        }
    }

    /**
     * 注册界面
     * @param $request
     * @return \View
     */
    public function registerPage(Request $request)
    {
        $recommend_code = $request->get('recommend_code','');
        $registerConf = PlayerRegisterConf::where('carrier_id', \WinwinAuth::currentWebCarrier()->id)->first();
        $playerAttr = [];
        //真实姓名
        if($registerConf->player_realname_conf_status){
            $playerAttr['real_name'] = 'real_name';
        }

        //出生日期
        if($registerConf->player_birthday_conf_status){
            $playerAttr['birthday'] = 'birthday';
        }
        //email
        if($registerConf->player_email_conf_status){
            $playerAttr['email'] = 'email';
        }
        //手机号
        if($registerConf->player_phone_conf_status){
            $playerAttr['mobile'] = 'mobile';
        }
        //qq号
        if($registerConf->player_qq_conf_status){
            $playerAttr['qq'] = 'qq';
        }
        //微信
        if($registerConf->player_wechat_conf_status){
            $playerAttr['wechat'] = 'wechat';
        }
        /*$playerExtenAttr = [
            'referral_code' => 'referral_code',
            'verification_code' => 'verification_code'
            ];*/
        //$playerAttr = array_merge($playerAttr, $playerExtenAttr);
        return \WTemplate::registerPage()->with(['recommend_code'=>$recommend_code, 'conf'=>$registerConf, 'playerAttr'=>$playerAttr]);
    }


    /**
     * 注册处理
     * @param CreatePlayer
     * @return \Response
     */
    //CreatePlayerRequest $request
    public function register(CreatePlayerRequest $request, PlayerRepository $playerRepository){
        if(empty($request->get('recommend_player_id'))){
            if(!\Captcha::check($request->get('verification_code'))){
                return $this->sendErrorResponse(['fields'=>'verification_code', 'message'=>'验证码输入错误'],403);
            }
        }


        $input = $request->all();
        if(\WinwinAuth::currentWebCarrier()->dashLoginConf->is_allow_player_register == 0) {
            return $this->sendErrorResponse('禁止注册',403);
        }

        //获取当前网址,判断是否为代理域名
        $webUrl = $request->header('host');
        $agentDomain = CarrierAgentDomain::with('agent.agentLevel')->where('website',$webUrl)->first();

        if ($agentDomain){
            $input['agent_id'] = $agentDomain->agent_id;
            $input['player_level_id'] = $agentDomain->agent->agentLevel->default_player_level;
        }else{
            $agent = CarrierAgentUser::where('is_default', true)->first();
            $agent &&  ($input['agent_id'] = $agent->id) && ($input['player_level_id'] = $agent->agent_level_id);
            $defaultPlayerLevel = CarrierPlayerLevel::isDefault()->first();
            $defaultPlayerLevel && $input['player_level_id'] = $defaultPlayerLevel->id;
        }

        //生成随机邀请码
        $input['referral_code'] = Player::generateReferralCode();

        //拼凑长链接,转换为短链接
        $requestUrl = $request->header('origin');
        $recommendUrl = $requestUrl.'/homes.registerPage'.'?recommend_code='.$input['referral_code'];
        $shortened = Player::getShortUrl($recommendUrl);

        //玩家推荐短链接
        $input['recommend_url'] = $shortened;

        //默认取款密码000000
        $input['pay_password'] = bcrypt('000000');

        $input['password'] = bcrypt($request->get('password'));

        //判断是否有推荐码,是则根据推荐码获得推荐会员ID
        $input['recommend_player_id'] = $request->get('referral_code') ? Player::where('referral_code',$request->get('referral_code'))->value('player_id') : 0;

        $input['carrier_id'] = \WinwinAuth::currentWebCarrier()->id;
        //判断是否是推荐用户开户
        if($request->get('recommend_player_id')){
            $input['recommend_player_id'] = $request->get('recommend_player_id');
            $this->playerLoginRepository->create($input);
            return $this->sendSuccessResponse();
        }
        try{
            $input['register_ip'] = RealIpHelper::getIp();
            //
            $player = $this->playerLoginRepository->create($input);
            \WinwinAuth::memberAuth()->loginUsingId($player->player_id);
            $this->playerLoginRepository->update(['login_at'=>Carbon::now()], $player->player_id);
            return $this->sendSuccessResponse(route('players.account-security'));
        }catch(\Exception $e){
            \WLog::error('会员注册失败:'.$e->getMessage().' '.$e->getFile());
            return $this->sendErrorResponse('注册失败', 403);
        }
    }

    /**
     * 验证码
     * @return \Response
     */
    public function captcha(){
        return  $this->sendResponse(\Captcha::img());
    }
    /*
     * 忘记密码界面
     * @return \View
     */
    public function forgetPassword()
    {
        return \WTemplate::forgetPasswordPage();
    }

    /**
     * 真人娱乐界面
     * @return \View
     */
    public function liveEntertainment()
    {
        return \WTemplate::liveEntertainmentGamePage();
    }

    /**
     * 老虎机界面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    /*public function slotMachine()
    {
        return \WTemplate::slotMachinePage();
    }*/

    /**
     * AG捕鱼界面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function agFish()
    {
        return \WTemplate::agFishPage();

    }

    /**
     * 体育投注界面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function sportsGames()
    {
        return \WTemplate::sportsGamesPage();

    }

    /**
     * 彩票投注界面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lotteryBetting()
    {
        return \WTemplate::lotteryBettingPage();
    }

    /**
     * 手机版界面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function mobile()
    {
        return \WTemplate::mobilePage();
    }

    /**
 * 优惠活动界面
 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
 */
    public function specialOffer()
    {
        return \WTemplate::benefitActivityPage();
    }

    /**
     * 关于我们界面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function aboutUs()
    {
        return \WTemplate::aboutUsPage();
    }

    /**
     * 联系我们界面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function contactUs()
    {
        return \WTemplate::contactUsPage();
    }

    /**
     * VIP制度界面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function vipSystem(\Request $request)
    {

        return \WTemplate::vipSystemPage();
    }

    /**
     * 常见问题界面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function FAQ()
    {
        return \WTemplate::questionAndAnswerPage();
    }

    /**
     * 隐私保护界面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function privacyProtection()
    {
        return \WTemplate::privacyProtectionPage();
    }

    /**
     * 博彩责任界面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function gamblingResponsibility()
    {
        return \WTemplate::gamblingResponsibilityPage();
    }

    /**
     * 服务责任界面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function termsOfService()
    {
        return \WTemplate::termsOfServicePage();
    }



    /**
     * 牌照展示界面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function licenseDisplay()
    {

        return \WTemplate::licenseDisplayPage();
    }

}
