<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\AppBaseController;
use App\Models\Player;
use App\Repositories\Carrier\PlayerRepository;
use App\Vendor\GameGateway\Gateway\GameGatewayRunTime;
use App\Vendor\GameGateway\PT\PTGameGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

/**
 * Created by PhpStorm.
 * User: winwin
 * Date: 2017/3/16
 * Time: 下午10:09
 */
class UserPerfectInformationController extends AppBaseController
{
    //修改登录密码
    public function resetPassword(Request $request){
        //查询输入账号是否存在
        $player = Player::where('player_id', $request->get('player_id'))->first();
        if ($player){
            if (\Hash::check($request->get('old_password'), $player->password) == true){
                $player->password = Hash::make($request->get('password'));
                $player->save();
                \WinwinAuth::memberAuth()->logout();
                return $this->sendSuccessResponse();
            }else{
                return $this->sendErrorResponse('密码错误!', 404);
            }
        }else{
            return $this->sendErrorResponse('账号状态异常!', 404);
        }
    }

    //修改取款密码
    public function resetWithdrawPassword(Request $request){
        //查询输入账号是否存在
        $player = Player::where('player_id', $request->get('player_id'))->first();
        if ($player->pay_password){
            if (\Hash::check($request->get('old_password'),$player->pay_password) == true){
                $player->pay_password = Hash::make($request->get('password'));
                $player->save();
                return $this->sendSuccessResponse();
            }else{
                return $this->sendErrorResponse('密码错误', 404);
            }
        }else{
            return $this->sendErrorResponse('取款密码未设置,请先设置取款密码', 404);
        }
    }
    //修改PT密码
    public function resetPtPassword(Request $request,PlayerRepository $playerRepository){
        //根据会员ID获得会员游戏平台账号表数据和其对应的主平台
        $player = $playerRepository->with('gameAccounts.mainGamePlat')->findWithoutFail($request->get('player_id'));
        if (empty($player)) {
            return $this->renderNotFoundPage();
        }
        //过滤掉其他平台,只获得PT
        $gameAccount = $player->gameAccounts->filter(function ($element){
            return $element->mainGamePlat->main_game_plat_code == PTGameGateway::getMainGamePlatCode();
        })->first();
        if(!$gameAccount){
            return $this->sendErrorResponse('该会员无PT游戏账户');
        }
        try{
            $gameRunTime = new GameGatewayRunTime(PTGameGateway::getMainGamePlatCode());
            $gameRunTime->updatePTPlayerPassword($player,$request->get('password'));
            return $this->sendSuccessResponse();
        }catch (\Exception $e){
            return $this->sendErrorResponse($e->getMessage());
        }
    }

}