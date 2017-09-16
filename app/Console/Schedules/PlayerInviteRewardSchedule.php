<?php

namespace App\Console\Schedules;
use App\Jobs\PlayerInviteRewardHandle;
use App\Models\Conf\CarrierInvitePlayerConf;
use App\Models\Player;
use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: wugang
 * Date: 2017/4/19
 * Time: 下午1:32
 */
class PlayerInviteRewardSchedule
{

    private $scheduleRecordFilePath = 'schedule/PlayerInviteRewardSchedulePlayerId';

    private $lastHandleSchedulePlayerId = 1;

    private $todayHasHandled = false;

    /**
     *一次查询500个会员
     */
    const QUERY_LENGTH_ONCE = 500;

    public function __construct()
    {
        //获取最近的玩家id
        if(\Storage::exists($this->scheduleRecordFilePath)){
            $data = json_decode(\Storage::get($this->scheduleRecordFilePath),true);
            if(isset($data['lastHandleFinishedTime'])){
                $this->todayHasHandled = $data['lastHandleFinishedTime'] == Carbon::today()->toDateString();
            }
            if(isset($data['latestHandleId'])){
                $this->lastHandleSchedulePlayerId = $data['latestHandleId'];
            }
        }
    }

    public function run(){
        if($this->todayHasHandled == false){
            $smallerPlayerId = $this->lastHandleSchedulePlayerId;
            $largerPlayerId  = $this->lastHandleSchedulePlayerId + self::QUERY_LENGTH_ONCE;
            \DB::beginTransaction();
            $playerMaxId = Player::lastPlayerId();
            $players = Player::lockForUpdate()->with('carrier')->wasInvited()->active()->idBetween($smallerPlayerId,$largerPlayerId)->get();
            $players->each(function(Player $player){
                dispatch(new PlayerInviteRewardHandle($player));
            });
            $this->lastHandleSchedulePlayerId = $largerPlayerId;
            if($this->lastHandleSchedulePlayerId >= $playerMaxId){
                \Storage::put($this->scheduleRecordFilePath,json_encode([
                    'lastHandleFinishedTime' => Carbon::today()->toDateString(),
                    'latestHandleId' => 1
                ]));
                $this->todayHasHandled = true;
            }else{
                $this->todayHasHandled = false;
                \Storage::put($this->scheduleRecordFilePath,json_encode([
                    'lastHandleFinishedTime' => Carbon::yesterday()->toDateString(),
                    'latestHandleId' => $this->lastHandleSchedulePlayerId
                ]));
            }
            \DB::commit();
        }
        //(new PlayerInviteRewardHandle($players->first()))->handle();
    }
}