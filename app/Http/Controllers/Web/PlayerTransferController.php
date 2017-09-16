<?php

namespace App\Http\Controllers\Web;

use App\Models\Def\MainGamePlat;
use App\Models\Log\PlayerAccountLog;
use App\Models\PlayerGameAccount;
use App\Vendor\GameGateway\Gateway\Exception\GameGateWayRuntimeException;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Player;
use App\Http\Controllers\AppBaseController;
use App\Vendor\GameGateway\Gateway\GameGatewayRunTime;

class PlayerTransferController extends AppBaseController
{

    /**
     * 账户转账界面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $player_id = \WinwinAuth::memberUser()->player_id;
        $player = Player::where('player_id', $player_id)->first();

        $mainGameList = MainGamePlat::active()->get();
        foreach($mainGameList as $k=>$game){
            $mainGameList = PlayerGameAccount::where('main_game_plat_id', $game->main_game_plat_id)->first();
            if($mainGameList){
                $game->amount = $mainGameList->amount;
                $game->account_id = $mainGameList->account_id;
            }else{
                $game->amount = 0;
                $game->account_id = 0;
            }

            $playerGameAccount[$k] = $game;
        }
        return \WTemplate::transferPage()->with(['main_account_amount'=>$player->main_account_amount, 'playerGameAccount'=>$playerGameAccount]);
    }

    /**
     * 账户转账
     * @param Request $request
     * @return \Response
     */
    public function accountTransfer(Request $request) {
        $amount = $request->get('amount');
        $transferFrom = $request->get('transferFrom');
        $transferTo = $request->get('transferTo');

        if(!in_array($transferFrom, ['main', 'pt']) || !in_array($transferTo, ['main', 'pt'])) {
            return $this->sendErrorResponse('目前支持主账户和PT转账');
        }else if($transferFrom == $transferTo){
            return $this->sendErrorResponse('同一平台不能转账');
        }

        $player = Player::findOrFail(\WinwinAuth::memberUser()->player_id);
        //TODO 自定义游戏平台code

        try{
            //主账户转入游戏账户
            if($transferFrom == MainGamePlat::MA && in_array($transferTo, MainGamePlat::$gamePlatCode)) {

                $gameRunTime = new GameGatewayRunTime($transferTo, GameGatewayRunTime::PRODUCTION);
                $gameRunTime->depositToPlayerGameAccount($player, $amount);

                return $this->getAmount($transferFrom, $transferTo);
            //游戏账户转入主账户
            }else if(in_array($transferFrom, MainGamePlat::$gamePlatCode) && $transferTo == MainGamePlat::MA) {

                $gameRunTime = new GameGatewayRunTime($transferFrom, GameGatewayRunTime::PRODUCTION);
                $gameRunTime->withDrawFromPlayerGameAccount($player, $amount);

                return $this->getAmount($transferFrom, $transferTo);
            //游戏账户转入游戏账户 //TODO 只有PT和主账户转账、存款
            }else{
                //转出
                $gameRunTimeFrom = new GameGatewayRunTime($transferFrom, GameGatewayRunTime::PRODUCTION);
                $gameRunTimeFrom->withDrawFromPlayerGameAccount($player, $amount);

                //转入
                $gameRunTimeTo = new GameGatewayRunTime($transferTo, GameGatewayRunTime::PRODUCTION);
                $gameRunTimeTo->depositToPlayerGameAccount($player ,$amount);
                return $this->getAmount($transferFrom, $transferTo);
            }
        }catch(\Exception $e){
            return $this->sendErrorResponse('系统错误,稍后再试...', 500);
        }
    }

    /**
     * 金额格式化
     * @param $amount
     * @return string
     */
    public function amountFormat($amount){
        $amount = number_format($amount, 2, ".", "");
        return $amount;
    }

    /**
     * 返回主账户,游戏账户金额
     * @param $transferFrom
     * @param $transferTo
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAmount($transferFrom, $transferTo, $is_recycel = false){
        $data = [];

        if($transferTo && $transferFrom){
            if($transferFrom == MainGamePlat::MA ){
                $transferToCode = MainGamePlat::where('main_game_plat_code', $transferTo)->first();
                $transferToAccount = PlayerGameAccount::where('main_game_plat_id', $transferToCode->main_game_plat_id)->where('player_id', \WinwinAuth::memberUser()->player_id)->first();
                $transferFromAccount = Player::where('player_id', \WinwinAuth::memberUser()->player_id)->first();
                $data = [
                    'mainAccount' => $transferFromAccount->main_account_amount,
                    'transferFromAccount' => $transferFromAccount->main_account_amount,
                    'transferToAccount' =>$transferToAccount->amount
                ];
            }elseif($transferTo == MainGamePlat::MA){
                $transferFromCode = MainGamePlat::where('main_game_plat_code', $transferFrom)->first();
                $transferFromAccount = PlayerGameAccount::where('main_game_plat_id', $transferFromCode->main_game_plat_id)->where('player_id', \WinwinAuth::memberUser()->player_id)->first();
                $transferToAccount = Player::where('player_id', \WinwinAuth::memberUser()->player_id)->first();
                $data = [
                    'mainAccount' => $transferToAccount->main_account_amount,
                    'transferFromAccount' => $transferFromAccount->amount,
                    'transferToAccount' =>$transferToAccount->main_account_amount
                ];
            }else{
                $transferFromCode = MainGamePlat::where('main_game_plat_code', $transferFrom)->first();
                $transferFromAccount = PlayerGameAccount::where('main_game_plat_id', $transferFromCode->main_game_plat_id)->where('player_id', \WinwinAuth::memberUser()->player_id)->first();
                $transferToCode = MainGamePlat::where('main_game_plat_code', $transferTo)->first();
                $transferToAccount = PlayerGameAccount::where('main_game_plat_id', $transferToCode->main_game_plat_id)->where('player_id', \WinwinAuth::memberUser()->player_id)->first();
                $mainAccount = Player::where('player_id', \WinwinAuth::memberUser()->player_id)->first();
                $data = [
                    'mainAccount' => $mainAccount->main_account_amount,
                    'transferFromAccount' => $transferFromAccount->amount,
                    'transferToAccount' =>$transferToAccount->amount
                ];
            }
        //游戏平台金额回收
        }elseif($is_recycel){
            $gamePlatAccount = PlayerGameAccount::where('player_id', \WinwinAuth::memberUser()->player_id)->with('mainGamePlat')->get();
            foreach ($gamePlatAccount as $gameAccount) {
                $data['gameAccount'][$gameAccount->mainGamePlat->main_game_plat_code] = $gameAccount->amount;
            }
            $mainAccount = Player::where('player_id', \WinwinAuth::memberUser()->player_id)->first();
            $data['mainAccount'] = $mainAccount->main_account_amount;
        }

        //dd($data);
        return $this->sendResponse($data);
    }

    /**
     * 转账中心回收
     * @throws \Exception
     */
    public function  accountRecycle(){
        $player_id = \WinwinAuth::memberUser()->player_id;
        $player = Player::where('player_id', $player_id)
                       ->with('gameAccounts.mainGamePlat')->get();
        try{
            foreach($player as $k=>$value){
                if($value->gameAccounts[$k]->amount > 0){
                    $gameRunTime = new GameGatewayRunTime($value->gameAccounts[$k]->mainGamePlat->main_game_plat_code, GameGatewayRunTime::PRODUCTION);
                    $gameRunTime->withDrawFromPlayerGameAccount($value, $value->gameAccounts[$k]->amount);
                }
            }
            return $this->getAmount(0, 0, true);
        }catch(\Exception $e){
           return $this->sendErrorResponse('系统错误,稍后再试...', 500);
        }


    }

    /**
     * 显示游戏主平台账户金额
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function accountRefresh(Request $request){

        $accountId = $request->get('accountId');
        if($accountId){
            $playerGameAccount = PlayerGameAccount::where('account_id', $accountId)
                ->where('player_id', \WinwinAuth::memberUser()->player_id)
                ->first();
            $amount = $playerGameAccount->amount;

        }else{
            $playerMainAccount = Player::where('player_id', \WinwinAuth::memberUser()->player_id)
                ->first();
            $amount = $playerMainAccount->main_account_amount;
        }

        return $this->sendResponse($amount);
    }

    /**
     * 一键转入
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function accountTransferOneTouch(Request $request){
        $transferTo = $request->get('transferTo');
        if(empty($transferTo)){
            return $this->sendErrorResponse('参数异常', 403);
        }
        $player = Player::where('player_id', \WinwinAuth::memberUser()->player_id)->first();
        try {
            $gameRunTime = new GameGatewayRunTime($transferTo, GameGatewayRunTime::PRODUCTION);
            $gameRunTime->depositToPlayerGameAccount($player, $player->main_account_amount);
            return $this->getAmount(MainGamePlat::MA, $transferTo);
        }catch(GameGateWayRuntimeException $e){
            return $this->sendErrorResponse($e->getMessage(), 403);
        }catch(\Exception $e){
            return $this->sendErrorResponse('系统错误,稍后再试...', 403);
        }
    }

    /**
     * 转账记录
     * @param Request $request
     * @return \View
     */
    public function transferRecords(Request $request)
    {
        $type = $request->get('type', '');
        $perPage = $request->get('perPage', 10);
        $start_time = $request->get('start_time', Carbon::now()->startOfMonth());
        $end_time = $request->get('end_time', Carbon::now()->endOfMonth());

        if(empty($start_time)){
            $start_time = '2000-01-01';
        }
        if(empty($end_time)){
            $end_time = Carbon::now();
        }
        $playerAccountLog = PlayerAccountLog::where('player_id', \WinwinAuth::memberUser()->player_id)
            ->whereDate('created_at', '>=', $start_time)
            ->whereDate('created_at', '<=', $end_time)
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);
        if($request->ajax()){
            if($type){
                return \WTemplate::transferLists()->with('playerAccountLog', $playerAccountLog);
            }
            return \WTemplate::transferRecords()->with('playerAccountLog', $playerAccountLog);
        }

    }


    /**
     * 转账记录删除
     * @param $id
     * @throws \Exception
     */
    public function transferRecordsDelete($id){
        $result = PlayerAccountLog::where('log_id', $id)->delete();
        if($result){
            return $this->sendSuccessResponse(route('players.transferRecords'));
        }else {
            return $this->sendErrorResponse('删除失败');
        }
    }

    /**
     * 转账记录批量删除
     * @param $log_id
     * @return \Response
     * @throws \Exception
     */
    function transferDropBatch(Request $request){
        if(empty($request->get('transferLogIdArr'))){
            return $this->sendErrorResponse('选择删除的记录', 403);
        }
        $result = PlayerAccountLog::whereIn('log_id', $request->get('transferLogIdArr') )->delete();
        if($result){
            return $this->sendSuccessResponse(route('players.transferRecords'));
        }else{
            return $this->sendErrorResponse('删除失败', 403);
        }
    }

}
