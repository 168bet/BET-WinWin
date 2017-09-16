<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\AppBaseController;
use App\Models\CarrierAgentUser;
use App\Models\Log\PlayerLoginLog;
use App\Models\Player;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AgentPerformanceController extends AppBaseController
{

    public function index(){

        $agent_id = \WinwinAuth::agentUser()->id;

        //1.查询代理下玩家会员总人数
        $agentMemberSum = Player::AgentMemberSum($agent_id)->count();

        //2.代理新增玩家会员人数
        $start_time = Carbon::now()->startOfDay();
        $end_time = Carbon::now()->endOfDay();
        $agentNewMemberSum = Player::AgentNewMemberSum($agent_id,$start_time,$end_time)->count();


        //3.活跃会员人数(当前时间减去上次登录时间不超过一个月)
        $now_time = Carbon::now();
        $last_month_time = Carbon::now()->subMonth();
        $activeMembers = Player::where('agent_id',$agent_id)->with(['loginLogs'=> function($query)use($last_month_time,$now_time){
            $query->LoginTime($last_month_time,$now_time);
        }])->get();

        $activeMember = 0;
        foreach ($activeMembers as $item){
            if (head($item->loginLogs)){
                $activeMember++;
            }
        }

        //4.代理推广点击
        $promoteClicks = CarrierAgentUser::where('id',$agent_id)->value('promotion_url_click_number');

        //5.首次存款次数&全部存款次数
        $depositNumbers = Player::where('agent_id',$agent_id)->withCount(['depositLogs' => function ($query){
            $query->payedSuccessfully();
        }])->get();

        //全部存款次数
        $depositNumber = 0;
        //首次存款次数
        $firstDepositNumber = 0;
        foreach ($depositNumbers as $item){
            if ($item->deposit_logs_count > 1){
                $item->first_deposit_count = 1;
            }else{
                $item->first_deposit_count = $item->deposit_logs_count;
            }
            $firstDepositNumber += ($item->first_deposit_count);
            $depositNumber += ($item->deposit_logs_count);
        }

        //6.全部存款金额
        $depositAmounts = Player::where('agent_id',$agent_id)->with(['depositLogs' => function($query){
            $query->payedSuccessfully();
        }])->get();

        $depositAmount = 0;
        foreach ($depositAmounts as $item){
            if (head($item->depositLogs)){
                $depositAmount += ($item->depositLogs->sum('amount'));
            }
        }


        //7.全部取款金额
        $withdrawAmounts = Player::where('agent_id',$agent_id)->with(['withdrawLogs' => function($query){
            $query->AccountOut();
        }])->get();

        $withdrawAmount = 0;
        foreach ($withdrawAmounts as $item){
            if (head($item->withdrawLogs)){
                $withdrawAmount += ($item->withdrawLogs->sum('finally_withdraw_amount'));
            }
        }

        //8.(PT平台)投注额&&(PT平台)公司输赢
        $ptBettingFlows = Player::where('agent_id',$agent_id)->with(['betFlowLogs' => function($query){
            $query->Pt();
        }])->get();

        $ptBettingAmount = 0;
        $ptCompanyWinAmount = 0;
        foreach ($ptBettingFlows as $item){
            if (head($item->betFlowLogs)){
                $ptBettingAmount += ($item->betFlowLogs->sum('available_bet_amount'));
                $ptCompanyWinAmount += ($item->betFlowLogs->sum('company_win_amount'));
            }
        }

        //9.(AG平台)投注额&&(AG平台)公司输赢
        $agBettingFlows = Player::where('agent_id',$agent_id)->with(['betFlowLogs' => function($query){
            $query->Ag();
        }])->get();

        $agBettingAmount = 0;
        $agCompanyWinAmount = 0;
        foreach ($agBettingFlows as $item){
            if (head($item->betFlowLogs)){
                $agBettingAmount += ($item->betFlowLogs->sum('available_bet_amount'));
                $agCompanyWinAmount += ($item->betFlowLogs->sum('company_win_amount'));
            }
        }

        //10.(MG平台)投注额&&(MG平台)公司输赢
        $mgBettingFlows = Player::where('agent_id',$agent_id)->with(['betFlowLogs' => function($query){
            $query->Mg();
        }])->get();

        $mgBettingAmount = 0;
        $mgCompanyWinAmount = 0;
        foreach ($mgBettingFlows as $item){
            if (head($item->betFlowLogs)){
                $mgBettingAmount += ($item->betFlowLogs->sum('available_bet_amount'));
                $mgCompanyWinAmount += ($item->betFlowLogs->sum('company_win_amount'));
            }
        }


        //11.有效投注总额&&公司总输赢
        $bettingFlows = Player::where('agent_id',$agent_id)->with('betFlowLogs')->get();
        $effectiveTotalBetting = 0;
        $companyWinAmount = 0;

        foreach ($bettingFlows as $item){
            if (head($item->betFlowLogs)){
                $effectiveTotalBetting += ($item->betFlowLogs->sum('available_bet_amount'));
                $companyWinAmount += ($item->betFlowLogs->sum('company_win_amount'));
            }
        }


        return view('Agent.agent_performance.index')->with([
            'agentMemberSum' => $agentMemberSum,
            'promoteClicks' => $promoteClicks,
            'agentNewMemberSum' => $agentNewMemberSum,
            'activeMember' => $activeMember,
            'depositNumber' => $depositNumber,
            'firstDepositNumber' => $firstDepositNumber,
            'depositAmount' => $depositAmount,
            'withdrawAmount' => $withdrawAmount,
            'ptBettingAmount' => $ptBettingAmount,
            'ptCompanyWinAmount' => $ptCompanyWinAmount,
            'agBettingAmount' => $agBettingAmount,
            'agCompanyWinAmount' => $agCompanyWinAmount,
            'mgBettingAmount' => $mgBettingAmount,
            'mgCompanyWinAmount' => $mgCompanyWinAmount,
            'effectiveTotalBetting' => $effectiveTotalBetting,
            'companyWinAmount' => $companyWinAmount,
        ]);
    }

}
