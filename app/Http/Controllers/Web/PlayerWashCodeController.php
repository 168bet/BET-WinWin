<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\AppBaseController;
use App\Models\Log\PlayerWithdrawLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Log\PlayerRebateFinancialFlow;

/**
 * Created by PhpStorm.
 * User: winwin
 * Date: 2017/3/16
 * Time: 下午10:09
 */
class PlayerWashCodeController extends AppBaseController
{

    public function washCodeRecords(Request $request){
        $type = $request->get('type', '');
        $status = $request->get('status');
        $perPage = $request->get('perPage', 10);
        $start_time = $request->get('start_time', Carbon::now()->startOfMonth());
        $end_time = $request->get('end_time', Carbon::now()->endOfMonth());
        if(is_numeric($status)){
            $status = (array)$status;
        }else{
            $status =array_keys(PlayerRebateFinancialFlow::statusMeta());
        }
        if(empty($start_time)){
            $start_time = '2000-01-01';
        }
        if(empty($end_time)){
            $end_time = Carbon::now();
        }

        $rebateFinancialStatus = PlayerRebateFinancialFlow::statusMeta();
        $playerRebateFinancialFlow = PlayerRebateFinancialFlow::where('player_id', \WinwinAuth::memberUser()->player_id)
            ->with('gamePlat')
            ->whereIn('is_already_settled', $status)
            ->whereBetween('created_at', [$start_time, $end_time])
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);

        if($request->ajax()){
            if($type){
                return \WTemplate::washCodeLists()->with('playerRebateFinancialFlow', $playerRebateFinancialFlow);
            }
            return \WTemplate::washCodeRecords()->with(['playerRebateFinancialFlow'=> $playerRebateFinancialFlow, 'rebateFinancialStatus'=>$rebateFinancialStatus]);
        }
    }
}