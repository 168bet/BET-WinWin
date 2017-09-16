<?php

namespace App\Http\Controllers\Web;

use App\Helpers\IP\RealIpHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Web\UpdatePlayerRequest;
use App\Models\Conf\CarrierWithdrawConf;
use App\Models\Def\BankType;
use App\Models\Log\PlayerBetFlowLog;
use App\Models\Log\PlayerDepositPayLog;
use App\Models\Log\PlayerWithdrawFlowLimitLog;
use App\Models\PlayerBankCard;
use App\Scopes\PlayerScope;
use App\Vendor\GameGateway\PT\PTGameGateway;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\CarrierActivity;
use App\Models\CarrierActivityAudit;
use App\Models\Def\GamePlat;
use App\Models\Conf\CarrierInvitePlayerConf;
use App\Models\Log\PlayerInviteRewardLog;


class PlayerCenterController extends AppBaseController
{
    /**
     * 会员中心
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function accountSecurity()
    {

        $player_id = \WinwinAuth::memberUser()->player_id;

        //根据会员ID获得会员游戏平台账号表数据和其对应的主平台
        $playerAccounts = Player::with('gameAccounts.mainGamePlat')->find($player_id);
        if (empty($playerAccounts)) {
            return $this->renderNotFoundPage();
        }
        //过滤掉其他平台,只获得PT
        $gameAccount = $playerAccounts->gameAccounts->filter(function ($element){
            return $element->mainGamePlat->main_game_plat_code == PTGameGateway::getMainGamePlatCode();
        })->first();


        $player = Player::with('registerConf')->where('player_id',$player_id)->first();
        return \WTemplate::accountSecurity()->with(['player' => $player,'gameAccount'=>$gameAccount]);
    }

    /**
     * 完善个人信息
     * @param Request $request
     */
    public function perfectUserInformation(UpdatePlayerRequest $request)
    {
        $player = Player::where('player_id', $request->get('player_id'))->first();
        if ($player){
            $player-> real_name = $request->get('real_name');
            $player->email= $request->get('email');
            $player->mobile = $request->get('mobile');
            $player->sex = $request->get('sex');
            $player->birthday = $request->get('birthday');
            $player->qq_account = $request->get('qq_account');
            $player->wechat = $request->get('wechat');
            $player->consignee= $request->get('consignee');
            $player->delivery_address = $request->get('delivery_address');
            $result = $player->save();
            if($result){
                return $this->sendSuccessResponse();
            }else{
                return $this->sendErrorResponse('信息保存失败', 404);
            }
        }else{
            return $this->sendErrorResponse('当前用户不存在', 404);
        }

    }

    /**
     * 财务中心
     * @return \View
     */
    public function financeCenter(){
        return \WTemplate::financeCenter();
    }

    /**
     * 会员取款
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function withdrawMoney(Request $request)
    {
        //当前运营商取款设置
        $carrierWithdrawConf = CarrierWithdrawConf::where('carrier_id', \WinwinAuth::currentWebCarrier()->id)->first();
        //查找默认银行取款渠道
        $banks = BankType::all();
        //查询当前玩家的取款账号
        $player_id = \WinwinAuth::memberUser()->player_id;
        //查询玩家完成流水数据,并检查是否完成流水
        $prompt_messages = '';
        $withDrawFlowRecords = PlayerWithdrawFlowLimitLog::unfinished()->with('limitGamePlats')->get();
        if(head($withDrawFlowRecords)){
            $withDrawFlowRecordCounts = PlayerWithdrawFlowLimitLog::unfinished()->count();
            if ($withDrawFlowRecordCounts > 0){
                $complete = 0;
                $unfinished = 0;
                foreach ($withDrawFlowRecords as $item){
                    $complete += ($item->complete_limit_amount);
                    $unfinished += ($item->limit_amount - $item->complete_limit_amount);
                }
                $prompt_messages .= ' 完成流水:'.$complete. '   未完成流水:'.$unfinished;
            }else{
                $prompt_messages = '已完成流水';
            }
        }else{
            $prompt_messages = '当前无流水数据';
        }


        //当前玩家所有的取款银行卡
        $playerBankCards = PlayerBankCard::with('bankType')->get();
        //处理银行卡号 *
        foreach ($playerBankCards as $playerBankCard){
            if ($playerBankCard->card_account){
                $playerBankCard->card_account = PlayerBankCard::replaceStar($playerBankCard->card_account,4,11);
            }
        }
        return \WTemplate::withdrawMoneyPage()->with(['banks'=>$banks,'playerBankCards'=>$playerBankCards,'carrierWithdrawConf'=>$carrierWithdrawConf,'withDrawFlowRecords'=>$withDrawFlowRecords,'prompt_messages'=>$prompt_messages]);
    }

    /**
     * 站内信
     * @param Request $request
     * @return \View
     */
    public function smsSubscriptions()
    {
        return \WTemplate::smsSubscriptions();
    }

    /**
     * 申请优惠
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function applyForDiscount()
    {
        $activityList = CarrierActivity::where('status', CarrierActivity::STATUS_SHELVES)
            ->where('is_website_display', CarrierActivity::WEBSITE_DISPLAY_IS)
            ->with(['activityAudit' => function($query){
                $query->waitingAudit();
            }])->get();

        //获取玩家已经参与的活动
        $activityList->each(function(CarrierActivity $activity){
            if($activity->activityAudit){
                $activity->canJoin = FALSE; return;
            }
            $maxJoinTimes = $activity->apply_times;
            if($maxJoinTimes != CarrierActivity::APPLY_TIMES_INFINITE){
                $activityAuditBuilder = CarrierActivityAudit::hasAudited()->byActivity($activity->id);
                $activityApplyTimes = 0;
                if($maxJoinTimes == CarrierActivity::APPLY_TIMES_EVERYDAY_ONCE){
                    $activityApplyTimes = $activityAuditBuilder->joinedToday()->count();
                }
                else if($maxJoinTimes == CarrierActivity::APPLY_TIMES_MONTHLY_ONCE){
                    $activityApplyTimes = $activityAuditBuilder->joinedThisMonth()->count();
                }
                else if($maxJoinTimes == CarrierActivity::APPLY_TIMES_WEEKLY_ONCE){
                    $activityApplyTimes = $activityAuditBuilder->joinedThisWeek()->count();
                }
                else if($maxJoinTimes == CarrierActivity::APPLY_TIMES_PERMANENT_ONCE){
                    $activityApplyTimes = $activityAuditBuilder->count();
                }
                if($activityApplyTimes >= 1){
                    $activity->canJoin = FALSE;
                    return;
                }
            }
            $activity->canJoin = TRUE;
        });

        //dd($activityList);

        return \WTemplate::applyForDiscount()->with('activityList', $activityList);
    }


    /**
     * 参加优惠活动
     */
    public function applyParticipate(Request $request) {
        $activity = CarrierActivity::findOrFail($request->get('act_id'));
        $loginPlayerId = \WinwinAuth::memberUser()->player_id;
        //检测是否是主动申请
        if($activity->is_active_apply == false){
            return $this->sendErrorResponse('该活动不需要主动申请');
        }
        try{
            $activity->checkUserCanApplyActivity($loginPlayerId,RealIpHelper::getIp());
        }catch (\Exception $e){
            return $this->sendErrorResponse($e->getMessage());
        }
        $carrierActivityAudit = new CarrierActivityAudit();
        $carrierActivityAudit->act_id = $activity->id;
        $carrierActivityAudit->carrier_id = \WinwinAuth::currentWebCarrier()->id;
        $carrierActivityAudit->player_id  = $loginPlayerId;
        $carrierActivityAudit->status = CarrierActivityAudit::STATUS_AUDIT;
        $carrierActivityAudit->ip = RealIpHelper::getIp();
        $carrierActivityAudit->save();
        return $this->sendResponse([],'参与成功,请等待客服审核');
    }

    /**
     * 财务报表
     * @return \View
     */
    public function financeStatistics(){
        return \WTemplate::financeStatistics();
    }

    /**
     * 取款记录
     * @return \View
     */
    public function withdrawRecords()
    {
        return \WTemplate::withdrawRecords();
    }


    /**
     * 优惠记录
     * @param $request
     * @return \View
     */
    public function discountRecords(Request $request)
    {
        $type =$request->get('type', '');
        $perPage = $request->get('perPage', 10);
        $start_time = $request->get('start_time', Carbon::now()->startOfMonth());
        $end_time = $request->get('end_time', Carbon::now()->endOfMonth());
        $status = $request->get('status');
        if(is_numeric($status)){
            $status = array($status);
        }else{
            $status = array_keys(CarrierActivityAudit::statusMeta());
        }

        if(empty($start_time)){
            $start_time = "2000-01-01 00:00:00";
        }
        if(empty($end_time)){
            $end_time = Carbon::now();
        }
        $carrierActivityAudit = CarrierActivityAudit::where('player_id', \WinwinAuth::memberUser()->player_id)
            ->with(['activity'=>function($query){
                $query->active()->with('actType');
            }])
            ->whereIn('status', $status)
            ->whereBetween('created_at', [$start_time, $end_time])
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);
        
        $carrierActivityStatus = CarrierActivityAudit::statusMeta();
        if($request->ajax()){
            if($type){
                return \WTemplate::discountLists()->with(['carrierActivityAudit'=>$carrierActivityAudit]);
            }
            return \WTemplate::discountRecords()->with(['carrierActivityAudit'=>$carrierActivityAudit, 'carrierActivityStatus'=>$carrierActivityStatus]);
        }
    }

    /**
     * 投注记录
     * @return \View
     */
    public function bettingRecords(Request $request)
    {
        $player_id = \WinwinAuth::memberUser()->player_id;
        $type = $request->get('type', '');
        $perPage = $request->get('perPage', 10);
        $start_time = $request->get('start_time', Carbon::now()->startOfMonth());
        $end_time = $request->get('end_time', Carbon::now()->endOfMonth());
        //获取当前用户投注记录中所有游戏平台ID
        $game_plat_ids = PlayerBetFlowLog::where('player_id',$player_id)->select('game_plat_id')->groupBy('game_plat_id')->get();
        $arr = array();
        foreach ($game_plat_ids as $item){
            $arr[] = $item->game_plat_id;
        }
        $game_plat_ids = $arr;
        $game_plat_id = $request->get('game_plat_id',$game_plat_ids);
        if ($game_plat_id == 0){
            $game_plat_id = $game_plat_ids;
        }
        if(!is_array($game_plat_id)){
            $game_plat_id = array($game_plat_id);
        }

        if(empty($start_time)){
            $start_time = "2000-01-01 00:00:00";
        }
        if(empty($end_time)){
            $end_time = Carbon::now();
        }

        $betFlowLogs = PlayerBetFlowLog::where('player_id',$player_id)
            ->with('gamePlat')
            ->selectRaw('game_plat_id,count(id) as count,sum(bet_amount) as bet_water,sum(available_bet_amount) as effective_bet ,sum(company_payout_amount) as payout,sum(company_win_amount) as income')
            ->whereIn('game_plat_id',$game_plat_id)
            ->whereBetween('created_at',[$start_time,$end_time])
            ->groupBy('game_plat_id')
            ->paginate($perPage);

        $gamePlat = GamePlat::all();

        if($request->ajax()){
            if($type){
                return \WTemplate::bettingLists()->with('betFlowLogs', $betFlowLogs);
            }
            return \WTemplate::bettingRecords()->with(['betFlowLogs'=>$betFlowLogs, 'gamePlat'=>$gamePlat]);
        }
    }




    /**
     * 投注详情
     * @param Request $request
     * @return \View
     */
    public function bettingDetails(Request $request)
    {
        $type = $request->get('type', '');
        $perPage = $request->get('perPage', 10);
        $gamePlatId = $request->get('gamePlatId');
        $start_time = $request->get('betting_detail_start', Carbon::now()->startOfMonth());
        $end_time = $request->get('betting_detail_end', Carbon::now()->endOfMonth());
        if(empty($start_time)){
            $start_time = "2000-01-01 00:00:00";
        }
        if(empty($end_time)){
            $end_time = Carbon::now();
        }

        $betFlowDetails = PlayerBetFlowLog::where('game_plat_id', $gamePlatId)
            ->with('game')
            ->whereBetween('created_at',[$start_time,$end_time])
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);


        $items = $betFlowDetails->items();
        array_walk($items,function($item){
            $name = $item->game->game_name;
            if ($item->player_or_banker != PlayerBetFlowLog::BET_FLOW_NONE){
                $name .= "\n(".PlayerBetFlowLog::betFlowBankerPlayerMeta()[$item->player_or_banker].")";
            }
            $item->bet_content = $name;
        });

        $data = [
            'betting_detail_start' => $start_time,
            'betting_detail_end' => $end_time,
            'gamePlatId' => $gamePlatId
        ];

        if($request->ajax()){
            if($type){
                return \WTemplate::bettingDetailLists()->with(['betFlowDetails'=>$betFlowDetails,'data'=>$data]);
            }
            return \WTemplate::bettingDetails()->with(['betFlowDetails'=>$betFlowDetails,'data'=>$data]);
        }
    }

    /**
     * 站内短信
     * @return \View
     */
    public function messageInStation()
    {
        return \WTemplate::messageInStation();
    }

    /**
     * 推荐好友
     * @return \View
     */
    public function friendRecommends(){
        return \WTemplate::friendRecommends();
    }

    /**
     * 我要推荐
     * @return \View
     */
    public function myRecommends()
    {
        $player_id = \WinwinAuth::memberUser()->player_id;
        $player = Player::where('player_id',$player_id)->first();
        //获取邀请会员数量
        $player->invite_player_count = Player::where('recommend_player_id',$player_id)->count();
        //累计获得奖金
        $player->totalBonus = PlayerInviteRewardLog::where('player_id', $player_id)->sum('reward_amount');
        return \WTemplate::myRecommends()->with('player', $player);
    }

    /**
     * 我的下线
     * @param integer $status 1本周 2本月
     * @param  $request
     * @return \View
     */
    public function myReferrals(Request $request)
    {
        $type = $request->get('type', '');
        $perPage = $request->get('perPage', 10);
        $start_time = $request->get('start_time', Carbon::now()->startOfMonth());
        $end_time = $request->get('end_time', Carbon::now()->endOfMonth());

        if(empty($start_time)){
            $start_time = "2000-01-01 00:00:00";
        }
        if(empty($end_time)){
            $end_time = Carbon::now();
        }
        $player = Player::where('recommend_player_id',\WinwinAuth::memberUser()->player_id)
            ->with('loginLogs')
            ->whereBetween('created_at',[$start_time, $end_time])
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);
        //dd($player->render());
        if($request->ajax()){
            if($type){
                return \WTemplate::myReferralLists()->with('player', $player);
            }
            return \WTemplate::myReferrals()->with('player', $player);
        }

    }

    /**
     * 账目统计
     * @param Request $request
     * @return \View
     */
    public function accountStatistics(Request $request)
    {
        $type = $request->get('type', '');
        $perPage  = $request->get('perPage', 10);
        $start_time = $request->get('start_time', Carbon::now()->startOfMonth());
        $end_time = $request->get('end_time', Carbon::now()->endOfMonth());

        if(empty($start_time)){
            $start_time = "2000-01-01 00:00:00";
        }
        if(empty($end_time)){
            $end_time = Carbon::now();
        }

        $recommentdPlayer = Player::where('recommend_player_id',\WinwinAuth::memberUser()->player_id)
            ->with(['betFlowLogs'=>function($query) use($start_time, $end_time){
                    $query->between($start_time, $end_time);
            }])
            ->with(['depositLogs'=>function($query) use($start_time, $end_time) {
                $query->between($start_time, $end_time);
            }])
            ->whereBetween('created_at', [$start_time, $end_time])
            ->orderBy('login_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);

        $statisticTotal = $this->statisticTotal($start_time, $end_time);
        if($request->ajax()){
            if($type){
                return \WTemplate::accountStatisticLists()->with('recommentdPlayer', $recommentdPlayer);
            }
            return \WTemplate::accountStatistics()->with(['recommentdPlayer'=>$recommentdPlayer, 'statisticTotal'=>$statisticTotal]);
        }
    }

    public function statisticDetails(Request $request){

        $type  = $request->get('type', '');
        $perPage  = $request->get('perPage', 10);
        $start_time = $request->get('start_time', Carbon::now()->startOfMonth());
        $end_time = $request->get('end_time', Carbon::now()->endOfMonth());

        if(empty($start_time)){
            $start_time = "2000-01-01 00:00:00";
        }
        if(empty($end_time)){
            $end_time = Carbon::now();
        }

        $statisticDetails = PlayerInviteRewardLog::where('player_id',\WinwinAuth::memberUser()->player_id)
            ->whereBetween('created_at',[$start_time,$end_time])
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);

        foreach($statisticDetails as $k=>$player){
            if($player->reward_related_player){
                $player_id = $player->reward_related_player;
            }else{
                $player_id = $player->player_id;
            }
            $username = Player::where('player_id', $player_id)->value('user_name');
            $statisticDetails[$k]->user_name = $username;
        }
        if($request->ajax()){
            if($type){
                return \WTemplate::accountStatisticDetailLists()->with('statisticDetails', $statisticDetails);
            }else{
                return \WTemplate::accountStatisticDetails()->with('statisticDetails', $statisticDetails);
            }

        }
    }

    /**
     * 账目总计
     * @param $start_time
     * @param $end_time
     * @return object
     *///->with('betFlowLogs')
    //->with('depositLogs')
    public function statisticTotal($start_time, $end_time){
        if(!empty($start_time)){
            $start_time = "2000-01-01 00:00:00";
        }
        if(!empty($end_time)){
            $end_time = Carbon::now();
        }
        $recommendPlayer = Player::where('recommend_player_id',\WinwinAuth::memberUser()->player_id)
            ->whereBetween('created_at', [$start_time, $end_time])
            ->get(['player_id']);
        //运营商有效会员配置
        $conf = CarrierInvitePlayerConf::first();
        //总的奖金
        $totalBonus = PlayerInviteRewardLog::withoutGlobalScope(PlayerScope::class)->where('player_id',\WinwinAuth::memberUser()->player_id)
            ->whereBetween('created_at', [$start_time, $end_time])
            ->sum('reward_amount');
        $statistic = (object)array();
        //总会员数
        $statistic->totalMembers = count($recommendPlayer);
        //奖金
        $statistic->totalBonus = $totalBonus;
        //有效会员
        $statistic->availableMembers = 0;
        //总存款额
        $statistic->totalDepositAmount = 0;
        //总投注额
        $statistic->totalBetAmount = 0;
        //总有效投注额
        $statistic->availableTotalBetAmount = 0;

//        $availableBetAmount = PlayerBetFlowLog::withoutGlobalScope(PlayerScope::class)->get();
//        dump($availableBetAmount);

        $data =Player::with(['betFlowLogs' => function($query){
            $query->withoutGlobalScope(PlayerScope::class);
        },'depositLogs' => function($query){
            $query->withoutGlobalScope(PlayerScope::class);
        }])->whereIn('player_id',$recommendPlayer->map(function(Player $element){ return $element->player_id;})->toArray())->get();

        foreach ($data as $item){
          if (head($item->betFlowLogs)){
              $statistic->totalBetAmount += ($item->betFlowLogs->sum('bet_amount'));
              $statistic->availableTotalBetAmount += ($item->betFlowLogs->sum('available_bet_amount'));
          }
          if (head($item->depositLogs)){
              $statistic->totalDepositAmount += ($item->depositLogs->sum('amount'));
          }
          if (($statistic->totalDepositAmount >= $conf->invalid_player_deposit_amount) && ($statistic->availableTotalBetAmount >= $conf->invalid_player_bet_amount)){
              $statistic->availableMembers += 1;
          }
        }
//        foreach($recommendPlayer as $value) {//->between($start_time, $end_time)
//            $availableBetAmount = PlayerBetFlowLog::where('player_id', $value->player_id)->sum('available_bet_amount');
//            $betAmount = PlayerBetFlowLog::where('player_id', $value->player_id)->sum('bet_amount');
//            $depositAmount = PlayerDepositPayLog::where('player_id', $value->player_id)->sum('amount');
//
//            if (($depositAmount >= $conf->invalid_player_deposit_amount) && ($availableBetAmount >= $conf->invalid_player_bet_amount)) {
//                $statistic->availableMembers += 1;
//            }
//
//            $statistic->totalDepositAmount += $depositAmount;
//            $statistic->totalBetAmount += $betAmount;
//            $statistic->availableTotalBetAmount += $availableBetAmount;
//        }
        return $statistic;
    }

    /**
     * 退出登陆
     * @return \\Redirector
     */
    public function logout(){
        \WinwinAuth::memberAuth()->logout();
        return  redirect(route('/'));
    }
}
