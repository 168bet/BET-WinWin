<?php

namespace App\Http\Controllers\Carrier;

use App\DataTables\Carrier\CarrierPlayerLevelDataTable;
use App\Http\Requests\Carrier;
use App\Http\Requests\Carrier\CreateCarrierPlayerLevelRequest;
use App\Http\Requests\Carrier\UpdateCarrierPlayerLevelRequest;
use App\Repositories\Carrier\CarrierGameRepository;
use App\Repositories\Carrier\CarrierPlayerLevelRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Response;
use App\Models\Def\PayChannelType;

class CarrierPlayerLevelController extends AppBaseController
{
    /** @var  CarrierPlayerLevelRepository */
    private $carrierPlayerLevelRepository;

    public function __construct(CarrierPlayerLevelRepository $carrierPlayerLevelRepo)
    {
        $this->carrierPlayerLevelRepository = $carrierPlayerLevelRepo;
    }

    /**
     * Display a listing of the CarrierPlayerLevel.
     *
     * @param CarrierPlayerLevelDataTable $carrierPlayerLevelDataTable
     * @return Response
     */
    public function index(CarrierPlayerLevelDataTable $carrierPlayerLevelDataTable)
    {
        return $carrierPlayerLevelDataTable->render('Carrier.carrier_player_levels.index');
    }

    /**
     * Show the form for creating a new CarrierPlayerLevel.
     *
     * @return Response
     */
    public function create()
    {
        return view('Carrier.carrier_player_levels.create');
    }

    /**
     * Store a newly created CarrierPlayerLevel in storage.
     *
     * @param CreateCarrierPlayerLevelRequest $request
     *
     * @return Response
     */
    public function store(CreateCarrierPlayerLevelRequest $request)
    {
        $input = $request->all();

        $input['carrier_id'] = \Auth::user()->carrier_id;

        $this->carrierPlayerLevelRepository->create($input);

        return $this->sendSuccessResponse(route('carrierPlayerLevels.index'));

    }

    /**
     * Display the specified CarrierPlayerLevel.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $carrierPlayerLevel = $this->carrierPlayerLevelRepository->findWithoutFail($id);

        if (empty($carrierPlayerLevel)) {
            Flash::error('Carrier Player Level not found');

            return redirect(route('carrierPlayerLevels.index'));
        }

        return view('Carrier.carrier_player_levels.show')->with('carrierPlayerLevel', $carrierPlayerLevel);
    }


    /**
     * 显示会员等级对应的游戏平台洗码信息
     * @param $id
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function rebateFlowShow($id){

        $carrierPlayerLevel = $this->carrierPlayerLevelRepository->with('rebateFinancialFlow.carrierGamePlat.gamePlat')->findWithoutFail($id);

        if (empty($carrierPlayerLevel)) {

            Flash::error('Carrier Player Level not found');

            return redirect(route('carrierPlayerLevels.index'));
        }


        return view('Carrier.carrier_player_levels.game_plats_flow_list')->with('carrierPlayerLevel', $carrierPlayerLevel);

    }

    /**
     * Show the form for editing the specified CarrierPlayerLevel.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {

        $carrierPlayerLevel = $this->carrierPlayerLevelRepository->findWithoutFail($id);

        if (empty($carrierPlayerLevel)) {

            Flash::error('Carrier Player Level not found');

            return redirect(route('carrierPlayerLevels.index'));
        }

        return view('Carrier.carrier_player_levels.edit')->with('carrierPlayerLevel', $carrierPlayerLevel);
    }

    /**
     * Update the specified CarrierPlayerLevel in storage.
     *
     * @param  int              $id
     * @param UpdateCarrierPlayerLevelRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCarrierPlayerLevelRequest $request)
    {
        $carrierPlayerLevel = $this->carrierPlayerLevelRepository->findWithoutFail($id);
        if (empty($carrierPlayerLevel)) {
            return $this->sendNotFoundResponse();
        }
        $this->carrierPlayerLevelRepository->update($request->all(), $id);
        return $this->sendSuccessResponse(route('carrierPlayerLevels.index'));
    }

    /**
     * Remove the specified CarrierPlayerLevel from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id,Request $request)
    {
        $carrierPlayerLevel = $this->carrierPlayerLevelRepository->findWithoutFail($id);
        if (empty($carrierPlayerLevel)) {
            return $this->sendNotFoundResponse();
        }
        $this->carrierPlayerLevelRepository->delete($id);
        return $this->sendSuccessResponse(route('carrierPlayerLevels.index'));
    }
    
    public function bankCardAll($id)
    {
        $data['parent_id']=0;
        //$data['id']= PayChannelType::COMPANY_PART_PAY;
        $payChannelType = PayChannelType::where($data)->get();
       foreach ($payChannelType as $key => $value){
           $type_ids[] = $value['id'];
       }
        $paySub = PayChannelType::whereIn('parent_id',$type_ids)->get();

        foreach ($paySub as $key => $value) {
            $ids[] = $value['id'];
        }

        $bankIds = \App\Models\Def\PayChannel::whereIn('pay_channel_type_id',$ids)->get();
        foreach ($bankIds as $key => $value) {
            $bank_ids[] = $value['id'];
        }

        $bankList = \App\Models\CarrierPayChannel::whereIn('def_pay_channel_id',$bank_ids)->get();
        $carrierPlayerLevel = $id;
        
        $carrierPlayerLevelBankCardIds = \App\Models\Map\CarrierPlayerLevelBankCardMap::where('carrier_player_level_id',$id)->get();
        $playerLevelBankCardIds = [];
        foreach ($carrierPlayerLevelBankCardIds as $key => $value) {
            $playerLevelBankCardIds[] = $value['carrier_pay_channle_id'];
        }
        return view('Carrier.carrier_player_levels.bankcardall')->with(['bankList'=>$bankList,'carrierPlayerLevel'=>$carrierPlayerLevel,'playerLevelBankCardIds'=>$playerLevelBankCardIds]);
    }
    
}
