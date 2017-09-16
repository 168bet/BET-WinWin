<?php

namespace App\Console;

use App\Console\Commands\HandlePlayerInviteReward;
use App\Console\Commands\HandlePlayerInviteRewardCommand;
use App\Console\Commands\PassPlayerRebateFinancialFlowDailyCommand;
use App\Console\Commands\PassPlayerRebateFinancialFlowWeekCommand;
use App\Console\Commands\SynchronizePTGameFlow;
use App\Console\Commands\SynchronizePTGameFlowCommand;
use App\Console\Commands\TestCommand;
use App\Console\Schedules\CarrierWinLoseStasticsSchedule;
use App\Console\Schedules\PlayerInviteRewardSchedule;
use App\Entities\CacheConstantPrefixDefine;
use App\Jobs\PlayerBetFlowHandle;
use App\Jobs\PlayerBetFlowSynchronizeFailHandle;
use App\Jobs\PlayerRebateFinancialFlowHandle;
use App\Jobs\SendReminderEmail;
use App\Models\Conf\CarrierInvitePlayerConf;
use App\Models\Player;
use App\Models\System\ReminderEmail;
use App\Vendor\GameGateway\Gateway\Exception\GameGateWaySynchronizeDBException;
use App\Vendor\GameGateway\Gateway\GameGatewayRunTime;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        HandlePlayerInviteRewardCommand::class,
        SynchronizePTGameFlowCommand::class,
        PassPlayerRebateFinancialFlowDailyCommand::class,
        PassPlayerRebateFinancialFlowWeekCommand::class,
        TestCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //本地测试环境不做会员游戏同步处理
        if(\App::environment() != 'local'){
            $schedule->command('synchronizeGameData:pt')->name('synchronizeGameDataPT');
        }

        $schedule->call(function (){
            try{
                $player = Player::online()->get();
                if($player->count() > 0){
                    \WLog::info('有'.$player->count().'个用户在线,开始检测登录状态是否过期');
                    $player->each(function (Player $element){
                        if(!\Cache::get(CacheConstantPrefixDefine::MEMBER_USER_ONLINE_REMEMBER_CACHE_PREFIX.$element->player_id)){
                            $element->is_online = false;
                            $element->save();
                        }
                    });
                }else{
                    \WLog::info('无在线用户');
                }
            } catch (\Exception $e){
                dispatch(new SendReminderEmail(new ReminderEmail($e)));
                \WLog::error('====>检测会员登陆状态失败',['message' => $e->getMessage()]);
            }
        })->everyMinute()->name('JudgeUserOnline');
        //会员邀请好友奖励定时任务处理
        $schedule->command('playerInviteReward:run')->name('HandleInvitePlayerReward')->everyMinute();
        //统计公司输赢,游戏输赢数据
        $schedule->call(function(){
            (new CarrierWinLoseStasticsSchedule())->run();
        })->everyFiveMinutes()->name('StasticCarrierWinLose');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
