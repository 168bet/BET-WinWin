<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['namespace' => 'Agent'],function(){
    Route::group(['prefix' => 'admin'],function (){
        Auth::routes();
    });
});

Route::group(['namespace' => 'Web'],function(){

    //---------------------WUGANG BEGIN -----------------------//
    \App\Vendor\GameGateway\Gateway\GameGatewayRunTime::route();
    \App\Vendor\Pay\Gateway\PayOrderRuntime::orderRouteList();
    //---------------------WUGANG END -----------------------//
        //author WQQ 2017-3-21 16:51:24
        //-----------------------WQQ BEGIN --------------------------//
        //顶部
        Route::get('homes.test', 'TestController@test')->name('homes.test');//测试
        Route::get('/', 'HomeController@index')->name('/');//首页
        Route::post('homes.login', 'HomeController@login')->name('member/login');//登陆
        Route::get('homes.registerPage', 'HomeController@registerPage')->name('homes.registerPage');//注册页面

        Route::post('homes.isAllowRegisterAjax', 'HomeController@isAllowRegisterAjax')->name('homes.isAllowRegisterAjax');//是否允许注册
        Route::post('homes.register', 'HomeController@register')->name('homes.register');//注册处理
        Route::get('homes.captcha', 'HomeController@captcha')->name('homes.captcha');//验证码
        Route::get('homes.forget-password', 'HomeController@forgetPassword')->name('homes.forget-password');//忘记密码

        //导航栏
        Route::get('homes.live-entertainment', 'HomeController@liveEntertainment')->name('homes.live-entertainment');//真人娱乐
        Route::get('homes.slot-machine', 'GameController@slotMachine')->name('homes.slot-machine');//老虎机
        Route::get('homes.ag-fish', 'HomeController@agFish')->name('homes.ag-fish');//AG捕鱼
        Route::get('homes.sports-games', 'HomeController@sportsGames')->name('homes.sports-games');//体育投注
        Route::get('homes.lottery-betting', 'HomeController@lotteryBetting')->name('homes.lottery-betting');//彩票投注
        Route::get('homes.mobile', 'HomeController@mobile')->name('homes.mobile');//手机版
        Route::get('homes.special-offer', 'HomeController@specialOffer')->name('homes.special-offer');//优惠活动

        //底部
        Route::get('homes.about-us', 'HomeController@aboutUs')->name('homes.about-us');//关于我们
        Route::get('homes.contact-us', 'HomeController@contactUs')->name('homes.contact-us');//联系我们
        Route::get('homes.vip-system', 'HomeController@vipSystem')->name('homes.vip-system');//VIP制度
        Route::get('homes.FAQ', 'HomeController@FAQ')->name('homes.FAQ');//常见问题
        Route::get('homes.privacy-protection', 'HomeController@privacyProtection')->name('homes.privacy-protection');//隐私保护
        Route::get('homes.gambling-responsibility', 'HomeController@gamblingResponsibility')->name('homes.gambling-responsibility');//博彩责任
        Route::get('homes.terms-of-service', 'HomeController@termsOfService')->name('homes.terms-of-service');//服务条款
        Route::get('homes.partners', 'HomeController@partners')->name('homes.partners');//合作伙伴
        Route::get('homes.license-display', 'HomeController@licenseDisplay')->name('homes.license-display');//拍照展示

        //代理前台路由
        Route::get('agents.index','AgentController@index')->name('agents.index');//首页
        Route::post('agents.login', 'AgentController@login')->name('agents.login');//登陆
        Route::get('agents.registerPage', 'AgentController@registerPage')->name('agents.registerPage');//注册页面
        Route::post('agents.register', 'AgentController@register')->name('agents.register');//注册处理
        Route::get('agents.captcha', 'AgentController@captcha')->name('agents.captcha');//验证码
        Route::get('agents.pattern','AgentController@pattern')->name('agents.pattern');//合营模式
        Route::get('agents.policy','AgentController@policy')->name('agents.policy');//佣金政策
        Route::get('agents.protocol','AgentController@protocol')->name('agents.protocol');//合营协议
        Route::get('agents.connectUs','AgentController@connectUs')->name('agents.connectUs');//联系我们
        Route::post('agents.dataAgentLevel','AgentController@dataAgentLevel')->name('agents.dataAgentLevel');//代理管理(代理类型二级联动)


        Route::group(['middleware' => ['auth:member']],function(){
            //会员中心
            Route::get('players.account-security', 'PlayerCenterController@accountSecurity')->name('players.account-security');//会员中心
            Route::post('userperfectinformation.resetPassword', 'UserPerfectInformationController@resetPassword')->name('userperfectinformation.resetPassword');//修改登录密码
            Route::post('userperfectinformation.resetWithdrawPassword', 'UserPerfectInformationController@resetWithdrawPassword')->name('userperfectinformation.resetWithdrawPassword');//修改取款密码
            Route::post('userperfectinformation.resetPtPassword', 'UserPerfectInformationController@resetPtPassword')->name('userperfectinformation.resetPtPassword');//修改PT密码
            Route::get('players.logout', 'PlayerCenterController@logout')->name('players.logout');//会员退出


            //会员中心
            Route::get('players.account-security', 'PlayerCenterController@accountSecurity')->name('players.account-security');//会员中心
            Route::post('players.perfectUserInformation', 'PlayerCenterController@perfectUserInformation')->name('players.perfectUserInformation');//完善个人信息
            Route::post('userperfectinformation.resetPassword', 'UserPerfectInformationController@resetPassword')->name('userperfectinformation.resetPassword');//修改登录密码
            Route::post('userperfectinformation.resetWithdrawPassword', 'UserPerfectInformationController@resetWithdrawPassword')->name('userperfectinformation.resetWithdrawPassword');//修改取款密码
            Route::post('userperfectinformation.resetPtPassword', 'UserPerfectInformationController@resetPtPassword')->name('userperfectinformation.resetPtPassword');//修改PT密码
            Route::get('players.logout', 'PlayerCenterController@logout')->name('players.logout');//会员退出

            //财务中心
            Route::get('players.financeCenter', 'PlayerCenterController@financeCenter')->name('players.financeCenter');//财务中心
            Route::get('players.deposit', 'PlayerDepositPayLogController@deposit')->name('players.deposit');//存款页面
            Route::get('players.withdraw-money', 'PlayerCenterController@withdrawMoney')->name('players.withdraw-money');//快速取款

            Route::post('playerwithdraw.addBankCard', 'PlayerWithdrawController@addBankCard')->name('playerwithdraw.addBankCard');//新增银行卡
            Route::post('playerwithdraw.deleteBankCard', 'PlayerWithdrawController@deleteBankCard')->name('playerwithdraw.deleteBankCard');//删除银行卡
            Route::post('playerwithdraw.withdrawQuotaCheck', 'PlayerWithdrawController@withdrawQuotaCheck')->name('playerwithdraw.withdrawQuotaCheck');//玩家取款限额检查
            Route::post('playerwithdraw.withdrawApply', 'PlayerWithdrawController@withdrawApply')->name('playerwithdraw.withdrawApply');//玩家取款限额检查

            Route::get('players.account-transfer', 'PlayerTransferController@index')->name('players.account-transfer');//转账中心页面
            Route::get('players.apply-for-discount', 'PlayerCenterController@applyForDiscount')->name('players.apply-for-discount');//申请优惠
            Route::post('players.applyParticipate', 'PlayerCenterController@applyParticipate')->name('players.applyParticipate');//申请参与优惠
            
            Route::get('players.rebateFinancialFlow', 'PlayerRebateFinancialFlowController@rebateFinancialFlow')->name('players.rebateFinancialFlow');//实时洗码
            Route::post('players.settleMoney', 'PlayerRebateFinancialFlowController@settleMoney')->name('players.settleMoney');//结算

            //财务报表
            Route::get('players.financeStatistics', 'PlayerCenterController@financeStatistics')->name('players.financeStatistics');//财务报表
            Route::get('players.depositRecords', 'PlayerDepositPayLogController@depositRecords')->name('players.depositRecords');//存款记录
            Route::get('players.withdrawRecords', 'PlayerWithdrawController@withdrawRecords')->name('players.withdrawRecords');//取款记录
            Route::get('players.transferRecords', 'PlayerTransferController@transferRecords')->name('players.transferRecords');//转账记录
            Route::get('players.washCodeRecords', 'PlayerWashCodeController@washCodeRecords')->name('players.washCodeRecords');//洗码记录
            Route::get('players.discountRecords', 'PlayerCenterController@discountRecords')->name('players.discountRecords');//优惠记录
            Route::get('players.bettingRecords', 'PlayerCenterController@bettingRecords')->name('players.bettingRecords');//投注记录
            Route::get('players.bettingDetails', 'PlayerCenterController@bettingDetails')->name('players.bettingDetails');//投注详情

            //客户服务
            Route::get('players.sms-subscriptions', 'PlayerCenterController@smsSubscriptions')->name('players.sms-subscriptions');//站内信
            Route::get('players.messageInStation', 'PlayerCenterController@messageInStation')->name('players.messageInStation');//站内短信

            //推荐好友
            Route::get('players.friendRecommends', 'PlayerCenterController@friendRecommends')->name('players.friendRecommends');//推荐好友
            Route::get('players.myRecommends', 'PlayerCenterController@myRecommends')->name('players.myRecommends');//我的推荐
            Route::get('players.myReferrals', 'PlayerCenterController@myReferrals')->name('players.myReferrals');//我的下线
            Route::get('players.accountStatistics', 'PlayerCenterController@accountStatistics')->name('players.accountStatistics');//账目统计
            Route::get('players.statisticDetails', 'PlayerCenterController@statisticDetails')->name('players.statisticDetails');//账目统计详情


            //存款
            Route::get('players.DepositTypePage', 'PlayerDepositPayLogController@DepositTypePage')->name('players.DepositTypePage');//不同存款界面
            Route::post('players.depositPayLogCreate', 'PlayerDepositPayLogController@depositPayLogCreate')->name('players.depositPayLogCreate');//存款处理
            Route::get('players.createWeChatQRcode/{id}', 'PlayerDepositPayLogController@createWeChatQRcode')->name('players.createWeChatQRcode');//存款成功跳转微信扫描界面
            Route::get('players.depositRecordsDelete/{id}', 'PlayerDepositPayLogController@depositRecordsDelete')->name('players.depositRecordsDelete');//删除存款记录
            Route::get('players.depositDropBatch', 'PlayerDepositPayLogController@depositDropBatch')->name('players.depositDropBatch');//批量删除存款记录

            //转账
            Route::post('players.accountTransfer', 'PlayerTransferController@accountTransfer')->name('players.accountTransfer');//转账处理
            Route::post('players.accountRecycle', 'PlayerTransferController@accountRecycle')->name('players.accountRecycle');//账户一键回收
            Route::get('players.accountRefresh', 'PlayerTransferController@accountRefresh')->name('players.accountRefresh');//刷新显示主游戏平台金额
            Route::get('players.accountTransferOneTouch', 'PlayerTransferController@accountTransferOneTouch')->name('players.accountTransferOneTouch');//一键转入
            Route::get('players.transferRecordsDelete/{id}', 'PlayerTransferController@transferRecordsDelete')->name('players.transferRecordsDelete');//转账记录删除
            Route::get('players.transferDropBatch', 'PlayerTransferController@transferDropBatch')->name('players.transferDropBatch');//转账记录批量删除

            //PT游戏
            Route::get('players.loginPTGame/{pt_game_code}', 'GameController@loginPTGame')->name('players.loginPTGame');
            Route::get('players.searchPtGame', 'GameController@searchPtGame')->name('players.searchPtGame');

            //登陆游戏
            //Route::get('players.loginGame', 'GameController@index')->name('players.loginGame');
        });
        //-----------------------WQQ END --------------------------//
        Route::get('orderTest','TestPayGatewayController@playerOrder');
});