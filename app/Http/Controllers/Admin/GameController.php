<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\GameDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateGameRequest;
use App\Http\Requests\Admin\UpdateGameRequest;
use App\Models\Carrier;
use App\Models\Def\Game;
use App\Models\Map\CarrierGame;
use App\Models\Map\CarrierGamePlat;
use App\Repositories\Admin\GameRepository;
use Carbon\Carbon;
use Flash;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Response;

class GameController extends AppBaseController
{
    /** @var  GameRepository */
    private $gameRepository;

    public function __construct(GameRepository $gameRepo)
    {
        $this->gameRepository = $gameRepo;
    }

    /**
     * Display a listing of the Game.
     *
     * @param GameDataTable $gameDataTable
     * @return Response
     */
    public function index(GameDataTable $gameDataTable)
    {
        return $gameDataTable->render('Admin.games.index');
    }

    /**
     * Show the form for creating a new Game.
     *
     * @return Response
     */
    public function create()
    {
        return view('Admin.games.create');
    }

    /**
     * Store a newly created Game in storage.
     *
     * @param CreateGameRequest $request
     *
     * @return Response
     */
    public function store(CreateGameRequest $request)
    {
        $input = $request->all();

        $game = $this->gameRepository->create($input);

        Flash::success('Game saved successfully.');

        return redirect(route('Admin.games.index'));
    }

    /**
     * Display the specified Game.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $game = $this->gameRepository->findWithoutFail($id);

        if (empty($game)) {
            Flash::error('Game not found');

            return redirect(route('Admin.games.index'));
        }

        return view('Admin.games.show')->with('game', $game);
    }

    /**
     * Show the form for editing the specified Game.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $game = $this->gameRepository->findWithoutFail($id);

        if (empty($game)) {
            Flash::error('Game not found');

            return redirect(route('Admin.games.index'));
        }

        return view('Admin.games.edit')->with('game', $game);
    }

    /**
     * Update the specified Game in storage.
     *
     * @param  int              $id
     * @param UpdateGameRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGameRequest $request)
    {
        $game = $this->gameRepository->findWithoutFail($id);
        if (empty($game)) {
            return $this->sendNotFoundResponse();
        }
        $this->gameRepository->update($request->all(), $id);
        return $this->sendSuccessResponse();
    }


    /**
     * 更新游戏状态
     * @param $gameId
     * @return mixed|Response
     */
    public function toggleGameStatus($gameId){
        $game = $this->gameRepository->findWithoutFail($gameId);
        if (empty($game)) {
            return $this->sendNotFoundResponse();
        }
        $game->status = !$game->status;
        $game->update();
        return $this->sendSuccessResponse();
    }


    public function showAssignCarriersModal(Request $request){
        $this->validate($request,[
            'game_ids' => 'required|array',
            'game_ids.*' => 'integer'
        ]);
        $games = Game::inIds($request->get('game_ids'))->get();
        if($games->count() == 1){
            $selectedCarriers = CarrierGame::byGameIds([$games->first()->game_id])->with('carrier')->get()->map(function($carrierGame){ return $carrierGame->carrier; });
        }else{
            $selectedCarriers = Collection::make([]);
        }
        $allCarriers = Carrier::all();
        return view('Admin.games.assign_carriers')->with('games',$games)->with('allCarriers',$allCarriers)->with('selectedCarriers',$selectedCarriers);
    }


    public function updateCarriersGames(Request $request){
        $this->validate($request,[
            'game_ids'      => 'required|array',
            'game_ids.*'    => 'integer',
            'carrier_ids'   => 'array',
            'carrier_ids.*' => 'integer'
        ]);
        $oldGameCarriers = CarrierGame::byGameIds($request->get('game_ids'))->get()->map(function($element){
            return $element->carrier_id;
        })->unique()->toArray();
        $carrierIds = $request->get('carrier_ids') ?: [];
        $deleteGameCarrierIds =  array_diff($oldGameCarriers,$carrierIds);
        $insertGameCarrierIds = array_diff($carrierIds,$oldGameCarriers);
        //dd($oldGameCarriers,$carrierIds,$deleteGameCarrierIds,$insertGameCarrierIds);
        try{
            \DB::transaction(function () use ($deleteGameCarrierIds,$insertGameCarrierIds,$request){
                $deleteGameCarrierIds && CarrierGame::byCarrierIds($deleteGameCarrierIds)->byGameIds($request->get('game_ids'))->delete();
                if($insertGameCarrierIds){
                    foreach ($request->get('game_ids') as $gameId){
                        $game = Game::findOrFail($gameId);
                        //从性能上面考虑, 不得已用DB操作
                        \DB::table('map_carrier_games')->insert(
                            array_map(function($element) use ($gameId,$game){
                                return [
                                    'carrier_id' => $element,
                                    'game_id' => $gameId,
                                    'display_name' => $game->game_name,
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                ];
                            },$insertGameCarrierIds)
                        );
                    }
                    //设置运营商游戏平台数据
                    foreach ($insertGameCarrierIds as $carrierId){
                        $carrierGamePlat = CarrierGamePlat::byCarrierId($carrierId)->get()->map(function ($element){ return $element->game_plat_id; })->toArray();
                        $gamePlat = CarrierGame::byCarrierIds([$carrierId])->with('game.gamePlat')->get()->map(function($element){ return
                            $element->game->gamePlat->game_plat_id;
                        })->unique()->toArray();
                        //dd($gamePlat,$carrierGamePlat);
                        $deleteGamePlats = array_diff($carrierGamePlat,$gamePlat);
                        $insertGamePlats = array_diff($gamePlat,$carrierGamePlat);
                        if($insertGamePlats){
                            foreach ($insertGamePlats as $insertGamePlat){
                                $carrierGamePlat = new CarrierGamePlat();
                                $carrierGamePlat->carrier_id = $carrierId;
                                $carrierGamePlat->game_plat_id = $insertGamePlat;
                                $carrierGamePlat->save();
                            }
                        }
                        $deleteGamePlats && CarrierGamePlat::byCarrierId($carrierId)->byGamePlats($deleteGamePlats)->delete();
                    }
                }
            });
            return $this->sendSuccessResponse();
        }catch (\Exception $e){
            throw  $e;
            return $this->sendErrorResponse($e->getMessage());
        }
        //dd($oldGameCarriers,$carrierIds,$deleteGameCarrierIds,$insertGameCarrierIds);
    }
    /**
     * Remove the specified Game from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $game = $this->gameRepository->findWithoutFail($id);

        if (empty($game)) {
            Flash::error('Game not found');

            return redirect(route('Admin.games.index'));
        }

        $this->gameRepository->delete($id);

        Flash::success('Game deleted successfully.');

        return redirect(route('Admin.games.index'));
    }
}
