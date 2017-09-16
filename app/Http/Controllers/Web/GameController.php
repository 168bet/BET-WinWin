<?php
/**
 * Created by PhpStorm.
 * User: winwin
 * Date: 2017/3/17
 * Time: 下午2:05
 */

namespace App\Http\Controllers\Web;
use App\Http\Controllers\AppBaseController;
use App\Models\Player;
use App\Vendor\GameGateway\PT\PTGameGateway;
use App\Models\Map\CarrierGame;
use App\Models\PlayerGameAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class GameController extends AppBaseController
{
    /**
     * PT登陆跳转
     * @param $ptGameCode
     * @return mixed
     */
    public function loginPTGame($ptGameCode){
        //http://cache.download.banner.greenjade88.com/casinoclient.html?language=ZH-CN&game=
        $ptGameGateway = new PTGameGateway();
        try{
            $gameAccount = $ptGameGateway->getPlayerAccount(\WinwinAuth::memberUser());
            $pageEntity  = $ptGameGateway->loginPageEntity($gameAccount,$ptGameCode);
            return view('Web.game_login')->with('playerLoginPageEntity',$pageEntity);
        }catch(\Exception $e){
            return $this->sendErrorResponse($e->getMessage());
        }
    }


    /**
     * 导航栏老虎机
     * @return mixed
     */
    public function slotMachine(Request $request){
        $ptGameName = $request->get('gameName', '');
        if($ptGameName){
            $ptGameList = CarrierGame::open()->where('display_name', 'like', '%'.$ptGameName.'%')->with(['game' => function($query){
                $query->open();
            }])->paginate(12);
        }else{
            $ptGameList = CarrierGame::open()->with(['game' => function($query){
                $query->open();
            }])->paginate(12);
        }

        if($request->ajax()){
            return \WTemplate::slotMachineList()->with('ptGameList', $ptGameList);
        };
        return \WTemplate::slotMachinePage()->with('ptGameList', $ptGameList);
    }

}
