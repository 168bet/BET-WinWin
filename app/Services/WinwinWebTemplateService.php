<?php
/**
 * Created by PhpStorm.
 * User: winwin
 * Date: 2017/3/28
 * Time: 下午9:57
 */

namespace App\Services;
use App\Entities\CacheConstantPrefixDefine;
use App\Models\Carrier;
use App\Models\Conf\CarrierWebBannerConf;
use App\Models\Conf\CarrierWebSiteConf;

class WinwinWebTemplateService
{
    /**
     * @var Carrier
     */
    private $carrier;
    /**
     * 网站基础配置项
     * @var CarrierWebSiteConf
     */
    private $carrierWebSiteConf;
    /**
     * 网站banner配置项
     * @var CarrierWebBannerConf[]
     */
    private $carrierWebBannerConfs;

    /**
     * WinwinWebTemplateService constructor.
     * @param $carrier
     */
    public function __construct($carrier)
    {
        $this->carrier = $carrier;
        $this->carrierWebSiteConf = \Cache::remember(CacheConstantPrefixDefine::CARRIER_SITE_CONF_CACHE_PREFIX.$this->carrier->id,3600,function (){
        return $this->carrier->webSiteConf;
    });
        $this->carrierWebBannerConfs = \Cache::remember(CacheConstantPrefixDefine::CARRIER_WEB_BANNER_CONF_CACHE_PREFIX.$this->carrier->id,3600,function (){
            return $this->carrier->webBannerConf;
        });
    }

    /**
     * 每页显示数据
     * @param $total 记录总数
     * @param $perPage 每页显示数量
     * @return string html
     */
    public function displayPerPage($total, $perPage){
        return '<div class="pager_tips">
                    共<span><font color="red">'. $total .'</font></span>条记录&nbsp;&nbsp;每页显示
          		    <select>
          			    <option value="5" '. ($perPage == 5 ? "selected" : "").'>5</option>
          			    <option value="10" '. ($perPage == 10 ? "selected" : "").'>10</option>
          			    <option value="25" '. ($perPage == 25 ? "selected" : "").'>25</option>
          			    <option value="50" '. ($perPage == 50 ? "selected" : "").'>50</option>
          			    <option value="100" '. ($perPage == 100 ? "selected" : "").'>100</option>
          		    </select>条
          	    </div>';
    }

    /**
     * 前端渲染404页面
     * @return \View
     */
    public function renderWebNotFoundPage() {
        return view('Web.default.errors.404');

    }

    /**
     * @return \View 首页
     */
    public function homePage(){
        return view('Web.default.home');
    }

    /**
     * @return \View 注册页面
     */
    public function registerPage(){
        return view('Web.default.login_registers.register');
    }

    /**
     * @return \View 忘记密码页面
     */
    public function forgetPasswordPage(){
        return view('Web.default.login_registers.forget_password');
    }

    /**
     * @return \View 真人娱乐页面
     */
    public function liveEntertainmentGamePage(){
        return view('Web.default.live_entertainments.live_entertainment');
    }


    /**
     * @return \View 老虎机页面
     */
    public function slotMachinePage(){
        return view('Web.default.slot_machines.slot_machine');
    }

    /**
     * @return \View 老虎机列表
     */
    public function slotMachineList(){
        return view('Web.default.slot_machines.slot_machine_list');
    }

    /**
     * @return \View AG捕鱼界面
     */
    public function agFishPage()
    {
        return view('Web.default.ag_fishs.ag_fish');
    }

    /**
     * @return \View 体育投注界面
     */
    public function sportsGamesPage()
    {
        return view('Web.default.sports_games.sports_games');
    }

    /**
     * @return \View 彩票投注界面
     */
    public function lotteryBettingPage()
    {
        return view('Web.default.lottery_bettings.lottery_betting');
    }

    /**
     * @return \View 手机版界面
     */
    public function mobilePage()
    {
        return view('Web.default.mobiles.mobile');
    }

    /**
     * @return \View 优惠活动界面
     */
    public function benefitActivityPage()
    {

        $actType = \App\Models\CarrierActivityType::where(['carrier_id'=>\WinwinAuth::currentWebCarrier()->id,'status'=>1])->get();
        $activityList = \App\Models\CarrierActivity::with("actType")->where(['carrier_id'=>\WinwinAuth::currentWebCarrier()->id,'status'=>1])->get();
        foreach ($activityList as $key => $value) {
            $activityList[$key]['img'] = \App\Models\Image\CarrierImage::select("image_path")->where(['id'=>$value['image_id']])->first();
        }
//        dd($activityList);
        return view('Web.default.special_offers.special_offer',['actType' => $actType,'activityList' => $activityList]);
    }

    /**
     * @return \View 关于我们界面
     */
    public function aboutUsPage()
    {
        return view('Web.default.about_us.about_us');
    }

    /**
     * @return \View 联系我们界面
     */
    public function contactUsPage()
    {
        return view('Web.default.about_us.contact_us');
    }

    /**
     * @return \View VIP制度界面
     */
    public function vipSystemPage()
    {
        return view('Web.default.about_us.vip_system');
    }

    /**
     * @return \View 常见问题界面
     */
    public function questionAndAnswerPage()
    {
        return view('Web.default.about_us.FAQ');
    }

    /**
     * @return \View 隐私保护界面
     */
    public function privacyProtectionPage()
    {
        return view('Web.default.about_us.privacy_protection');
    }

    /**
     * @return \View 博彩责任界面
     */
    public function gamblingResponsibilityPage()
    {
        return view('Web.default.about_us.gambling_responsibility');
    }

    /**
     * @return \View 服务责任界面
     */
    public function termsOfServicePage()
    {
        return view('Web.default.about_us.terms_of_service');
    }

    /**
     * @return \View 牌照展示界面
     */
    public function licenseDisplayPage()
    {
        return view('Web.default.about_us.license_display');
    }

    /**
     * @return \View 财务中心
     */
    public function financeCenter(){
        return view('Web.default.player_centers.finance_center');
    }

    /**
     * @return \View 存款界面
     */
    public function depositPage()
    {
        return view('Web.default.player_centers.finance_center.deposit');
    }

    /**
     * @return \View 在线存款
     */
    public function onlineDeposit()
    {
        return view('Web.default.player_centers.finance_center.deposits.online');
    }

    /**
     * @return \View 扫码支付存款
     */
    public function scanCodeDeposit()
    {
        return view('Web.default.player_centers.finance_center.deposits.scan_code');
    }

    /**
     * @return \View 线下银行转账存款
     */
    public function bankTransferDeposit()
    {
        return view('Web.default.player_centers.finance_center.deposits.bank_transfer');
    }

    /**
     * @return \View 扫码支付(公司)存款
     */
    public function scanCodeCompanyDeposit()
    {
        return view('Web.default.player_centers.finance_center.deposits.scan_code_company');
    }

    /**
     * @return \View 点卡支付存款
     */
    public function pointCardDeposit()
    {
        return view('Web.default.player_centers.finance_center.deposits.point_card');
    }

    /**
     * @return \View 在线支付/扫码支付存款
     */
    public function onlineScanDeposit()
    {
        return view('Web.default.player_centers.finance_center.deposits.online_scan');
    }



    /**
     * @return \View 取款界面
     */
    public function withdrawMoneyPage()
    {
        return view('Web.default.player_centers.finance_center.withdraw_money');
    }

    /**
     * @return \View 转账界面
     */
    public function transferPage()
    {
        return view('Web.default.player_centers.finance_center.account_transfer');
    }

    /**
     * @return \View 微信扫码界面
     */
    public function wechatScanPage()
    {
        return view('Web.default.player_centers.finance_center.wechat_scan');
    }

    /**
     * @return \View 申请优惠
     */
    public function applyForDiscount()
    {
        return view('Web.default.player_centers.finance_center.apply_for_discount');
    }

    /**
     * @return \View 实时洗码
     */
    public function rebateFinancialFlowRecord()
    {
        return view('Web.default.player_centers.finance_center.rebate_financial_flow.records');
    }

    /**
     * @return \View
     */
    public function rebateFinancialFlowList()
    {
        return view('Web.default.player_centers.finance_center.rebate_financial_flow.lists');
    }

    /**
     * @return \View 财务报表
     */
    public function financeStatistics()
    {
        return view('Web.default.player_centers.finance_statistics');
    }

    /**
     * @return \View 存款记录
     */
    public function withdrawRecords()
    {
        return view('Web.default.player_centers.finance_statistic.withdraw.records');
    }

    /**
     * @return \View 存款记录
     */
    public function withdrawLists()
    {
        return view('Web.default.player_centers.finance_statistic.withdraw.lists');
    }

    /**
     * @return \View 取款记录
     */
    public function depositRecords()
    {
        return view('Web.default.player_centers.finance_statistic.deposit.records');
    }

    /**
     * 列表数据
     * @return \View 取款记录
     */
    public function depositLists()
    {
        return view('Web.default.player_centers.finance_statistic.deposit.lists');
    }

    /**
     *
     * @return \View 转账记录
     */
    public function transferRecords()
    {
        return view('Web.default.player_centers.finance_statistic.transfer.records');
    }

    /**
     * 列表数据
     * @return \View 转账记录
     */
    public function transferLists()
    {
        return view('Web.default.player_centers.finance_statistic.transfer.lists');
    }

    /**
     * @return \View 洗码记录
     */
    public function washCodeRecords()
    {
        return view('Web.default.player_centers.finance_statistic.wash_code.records');
    }

    /**
     * 列表数据
     * @return \View 洗码记录
     */
    public function washCodeLists()
    {
        return view('Web.default.player_centers.finance_statistic.wash_code.lists');
    }

    /**
     * @return \View 优惠记录
     */
    public function discountRecords()
    {
        return view('Web.default.player_centers.finance_statistic.discount.records');
    }

    /**
     * 列表数据
     * @return \View 优惠记录
     */
    public function discountLists()
    {
        return view('Web.default.player_centers.finance_statistic.discount.lists');
    }

    /**
     * @return \View 投注记录
     */
    public function bettingRecords()
    {
        return view('Web.default.player_centers.finance_statistic.betting.records');
    }

    /**
     * 列表数据
     * @return \View 投注记录
     */
    public function bettingLists()
    {
        return view('Web.default.player_centers.finance_statistic.betting.lists');
    }

    /**
     * @return \View 投注详情
     */
    public function bettingDetails()
    {
        return view('Web.default.player_centers.finance_statistic.betting.details');
    }

    /**
     * 数据列表
     * @return \View 投注详情
     */
    public function bettingDetailLists()
    {
        return view('Web.default.player_centers.finance_statistic.betting.detail_lists');
    }

    /**
     * 账户安全
     * @return \View
     */
    public function accountSecurity(){
        return view('Web.default.player_centers.account_security');
    }

    /**
     * 站内短信
     * @return \View
     */
    public function messageInStation(){
        return view('Web.default.player_centers.message_in_station');
    }

    /**
     * 站内短信
     * @return \View
     */
    public function smsSubscriptions(){
        return view('Web.default.player_centers.sms_subscriptions');
    }

    /**
     * 我要推荐
     * @return \View
     */
    public function friendRecommends(){
        return view("Web.default.player_centers.friend_recommends");
    }

    /**
     * 我要推荐
     * @return \View
     */
    public function myRecommends(){
        return view("Web.default.player_centers.friend_recommend.my_recommend.records");
    }

    /**
     * 我的下线
     * @return \View
     */
    public function myReferrals(){
        return view("Web.default.player_centers.friend_recommend.my_referral.records");
    }

    /**
     * 我的下线列表
     * @return \View
     */
    public function myReferralLists(){
        return view("Web.default.player_centers.friend_recommend.my_referral.lists");
    }

    /**
     * 账目统计
     * @return \View
     */
    public function accountStatistics(){
        return view("Web.default.player_centers.friend_recommend.account_statistic.records");
    }

    /**
     * 账目统计列表
     * @return \View
     */
    public function accountStatisticLists(){
        return view("Web.default.player_centers.friend_recommend.account_statistic.lists");
    }

    /**
 * 账目统计详情
 * @return \View
 */
    public function accountStatisticDetails(){
        return view("Web.default.player_centers.friend_recommend.account_statistic.details");
    }

    /**
     * 账目统计详情列表
     * @return \View
     */
    public function accountStatisticDetailLists(){
        return view("Web.default.player_centers.friend_recommend.account_statistic.detail_lists");
    }

    /**
     * @return string 网站标题
     */
    public function title(){
        return $this->carrierWebSiteConf->site_title;
    }

    /**
     * @return string 网站关键字
     */
    public function keywords(){
        return $this->carrierWebSiteConf->site_key_words;
    }

    /**
     * @return null|string 网站联系我们html内容
     */
    public function contactUsHtmlContent(){
        return $this->carrierWebSiteConf->contact_us();
    }



}