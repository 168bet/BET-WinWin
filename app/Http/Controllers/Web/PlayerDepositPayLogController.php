<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Web\CreatePlayerDepositPayLogRequest;
use App\Models\CarrierPayChannel;
use App\Models\Def\PayChannel;
use App\Models\Log\PlayerDepositPayLog;
use App\Models\PlayerBankCard;
use App\Repositories\Web\PlayerDepositPayLogRepository;
use App\Vendor\Pay\Gateway\PayOrderInterface;
use App\Vendor\Pay\Gateway\PayOrderRuntime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Vendor\Pay\OfflineDeposit\OfflineDepositOrderGateway;
use App\Models\Def\PayChannelType;
use App\Models\Player;
use App\Models\Def\BankType;
use App\Models\CarrierActivity;
use App\Models\CarrierPlayerLevel;

/**
 * Created by PhpStorm.
 * User: winwin
 * Date: 2017/3/16
 * Time: 下午10:09
 */
class PlayerDepositPayLogController extends AppBaseController
{
    private $depositPayLogRepository;
    public function __construct(PlayerDepositPayLogRepository $depositPayLogRepository)
    {
        $this->depositPayLogRepository = $depositPayLogRepository;
    }


    /**
     * 会员存款界面
     * @return \View
     */
    public function deposit()
    {
        //用户等级
        $playerLevelId = \WinwinAuth::memberUser()->player_level_id;

        $PlayerLevel = CarrierPlayerLevel::find($playerLevelId)->with(['bankCardMap'=>function($query){
            $query->with(['carrierBankCards'=>function($query){
                $query->available();
            }]);
        }])->first();



        $onlinePayList = array();

//        //运营商默认支付渠道--三方支付
//        $onlinePay = CarrierPayChannel::where('binded_third_part_pay_id','!=',null)->with('bindedThirdPartGateway.defPayChannel.payChannelType')->get();
//
//        foreach ($onlinePay as $item){
//            if($item->bindedThirdPartGateway && $item->bindedThirdPartGateway->defPayChannel->payChannelType->isThirdPartPay()){
//                    $onlinePayList[$item->bindedThirdPartGateway->defPayChannel->payChannelType->id][] = $item;
//               }
//        }

        $otherPayList = array();

        //根据玩家等级查找银行卡支付渠道
        foreach($PlayerLevel->bankCardMap as $map){
            if($map->carrierBankCards){
                //第三方在线支付(绑定第三方)
                $onlinePay = CarrierPayChannel::where('id', $map->carrierBankCards->id)->with('bindedThirdPartGateway.defPayChannel.payChannelType')->first();
                if($onlinePay->bindedThirdPartGateway && $onlinePay->bindedThirdPartGateway->defPayChannel->payChannelType->isThirdPartPay()){
                    $onlinePayList[$onlinePay->bindedThirdPartGateway->defPayChannel->payChannelType->id][] = $onlinePay;
                    //其他支付(未绑定第三方)
                }else{
                    $otherPay = CarrierPayChannel::where('id', $map->carrierBankCards->id)->with('PayChannel.payChannelType')->first();
                    $otherPayList[$otherPay->PayChannel->payChannelType->id][] = $otherPay;
                }
            }
        }

        //dd($otherPayList);
        /*$pay_channel_type_id = 0;
        $onlinePayList = CarrierPayChannel::available()->with('bindedThirdPartGateway.defPayChannel.payChannelType')->orderBy('created_at', 'DESC')->get();
        foreach ($onlinePayList as $k=>$pay) {

            //绑定第三方
            if($pay->bindedThirdPartGateway && $pay->bindedThirdPartGateway->defPayChannel->payChannelType->isThirdPartPay()){
                $onlinePay[$pay->bindedThirdPartGateway->defPayChannel->payChannelType->id][] = $pay;
                $pay_channel_type_id = $pay->bindedThirdPartGateway->defPayChannel->payChannelType->id;
            }
        }*/

        //dd($onlinePayList);
        //其他支付
        /*$otherPayList = CarrierPayChannel::available()->with('PayChannel.payChannelType')->orderBy('created_at', 'DESC')->get();
        foreach ($otherPayList as $k=>$pay) {
            //没有绑定第三方
            if(empty($pay->bindedThirdPartGateway)){
                $otherPay[$pay->PayChannel->payChannelType->id][] = $pay;
                unset($otherPay[$pay_channel_type_id]);
            }
        }*/

        $array = [
            'onlinePayList' => $onlinePayList,
            'otherPayList' => $otherPayList,
        ];

        return \WTemplate::depositPage()->with($array);
    }

    /**
     * 会员存款操作
     * @param CreatePlayerDepositPayLogRequest $request
     */
    public function depositPayLogCreate(CreatePlayerDepositPayLogRequest $request) {
        if(\WinwinAuth::currentWebCarrier()->depositConf->is_allow_player_deposit == 0){
            return $this->sendErrorResponse('禁止存款', 403);
        }


        $payChannelTypeId = $request->get('payChannelTypeId');

        //微信扫码
        if($payChannelTypeId ==  PayChannelType::SCAN_CODE_PAY){
            return $this->scanPay($request);
        //线下银行转账
        }elseif($payChannelTypeId == PayChannelType::BANK_TRANSFER_PAY){
            return $this->offlineTransferDeposit($request);
        //在线支付
        }elseif($payChannelTypeId == PayChannelType::ONLINE_PAY){
            return $this->onlineTransferDeposit($request);
        }
    }


    public function onlineTransferDeposit(Request $request){

        $amount = $request->get('amount');
        $carrierPayChannelId = $request->get('carrierPayChannelId');
        $activityId = $request->get('activityId');
        $carrierPayChannel = CarrierPayChannel::find($carrierPayChannelId);
        if(!$carrierPayChannel || $carrierPayChannel->payChannel->payChannelType->isThirdPartPay() == false){
            return $this->sendErrorResponse('运营商支付渠道有误', 403);
        }
        try {
            \DB::beginTransaction();
            $payOrderRuntime = new PayOrderRuntime(\WinwinAuth::memberUser(), $carrierPayChannel, $amount);
            $response = $payOrderRuntime->createOrder(null, null,$request->get('bankCode'));
            if($activityId){
                $activity = CarrierActivity::findOrFail($activityId);
                $response->payOrder->carrier_activity_id = $activity->id;
            }
            $response->payOrder->update();
            \DB::commit();

            $redirectUrl = route('players.DepositTypePage', ['id'=>$response->payOrder->id, 'pay_url'=>urlencode($response->payUrl)]);
            return $this->sendResponse($redirectUrl);
        }catch(\Exception $e){
            \DB::rollBack();
            return $this->sendErrorResponse($e->getMessage());
        }

    }


    public function offlineTransferDeposit(Request $request){

        $player_id = \WinwinAuth::memberUser()->player_id;
        $cardId = $request->get('cardId', '');
        $amount = $request->get('amount');
        $useName = $request->get('useName');
        $bankTypeId = $request->get('bankTypeId');
        $bankAccount = $request->get('bankAccount');
        $depositTime = $request->get('depositTime');
        $depositType = $request->get('depositType');
        $carrierPayChannelId = $request->get('carrierPayChannelId');
        $activityId = $request->get('activityId');

        $carrierPayChannel = CarrierPayChannel::find($carrierPayChannelId);
        $carrierActivity = CarrierActivity::find($activityId);
        $offlineDepositOrderGateway =  new OfflineDepositOrderGateway();

        if(!$carrierPayChannel){
            return $this->sendErrorResponse('运营商支付渠道有误', 403);
        }

        \DB::beginTransaction();
        try {
            if (!$cardId && PlayerBankCard::bankAccount($bankAccount)) {//先添加银行再存款
                $player_bank_card = new PlayerBankCard();
                $player_bank_card->carrier_id = \WinwinAuth::currentWebCarrier()->id;
                $player_bank_card->player_id = \WinwinAuth::memberUser()->player_id;
                $player_bank_card->card_account = $bankAccount;
                $player_bank_card->card_type = $bankTypeId;
                $player_bank_card->card_owner_name = $useName;
                $player_bank_card->card_birth_place = '无';
                $player_bank_card->created_at = Carbon::now();
                $player_bank_card->save();
                $playerBankCard = PlayerBankCard::where('card_account', $bankAccount)->first();
            } else {
                $playerBankCard = PlayerBankCard::find($cardId);
            }
            if(!$playerBankCard){
                return $this->sendErrorResponse('存款银行卡有误');
            }
            $payOrderRuntime = new PayOrderRuntime(\WinwinAuth::memberUser(), $carrierPayChannel, $amount, $playerBankCard);
            $response = $payOrderRuntime->createOrder($depositTime, $depositType);
            if($activityId){
                $activity = CarrierActivity::findOrFail($activityId);
                $response->payOrder->carrier_activity_id = $activity->id;
            }
            $response->payOrder->update();
            \DB::commit();
            return $this->sendResponse(route('players.financeStatistics') . '#deposit-record');
        }catch(\Exception $e){
            \DB::rollBack();
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    /**
     * 扫码支付(个人、公司)
     * @param int $pay_channel_id
     * @return \Illuminate\Http\JsonResponse|\Response
     */
    public function scanPay(Request $request){

        $pay_channel_id = $request->get('payChannelId');
        $amount = $request->get('amount');
        //TODO $pay_channel_id = 19 微信扫码测试
        $payChannel = CarrierPayChannel::where('def_pay_channel_id', $pay_channel_id = 19)->first();//$pay_channel_id
        try{
            $payGateWay = new PayOrderRuntime(\WinwinAuth::memberUser(), $payChannel, $amount);
            $orderResult = $payGateWay->createOrder();
            //dd($orderResult);
            $redirectUrl = route('players.createWeChatQRcode', ['id'=>$orderResult->payOrder->id/*, 'pay_url'=>urlencode($orderResult->payUrl)*/]);
            // dd($this->sendResponse($redirectUrl));
            return $this->sendResponse($redirectUrl);
        }catch(\Exception $e){
            //throw $e;
            return $this->sendErrorResponse($e->getMessage(), 403);
        }
    }

    /**
     * 微信二维码界面
     * @param Request $request
     */
    public function createWeChatQRcode($id){
        $playerDepositPay = $this->depositPayLogRepository->find($id, ['*']);
        if($playerDepositPay) {
            return \WTemplate::wechatScanPage()->with('playerDepositPay', $playerDepositPay);
        }else {
            return $this->sendErrorResponse('订单不存在', 403);
        }
    }

    /**
     * 不同存款类型界面
     * @param Request $request
     * @return \View|void
     */
    public function DepositTypePage(Request $request){
        $payChannelTypeId = $request->get('payChannelTypeId');
        $carrierPayChannelId = $request->get('carrierPayChannelId');


        //获取银行卡号信息
        $player_id = \WinwinAuth::memberUser()->player_id;
        $player = Player::where('player_id', $player_id)->with(['bankCards'=> function($query){
                $query->active()->with('bankType');
        }])->get();

        //银行列表
        $bankList = BankType::all();

        //活动列表
        $carrierActivityList = CarrierActivity::active()
            ->where('is_deposit_display', CarrierActivity::DEPOSIT_DISPLAY_IS)
            ->get();

        $otherPay =  CarrierPayChannel::where('id', $carrierPayChannelId)->with('PayChannel.payChannelType')->first();

        $other = [
            'otherPay' => $otherPay,
            'carrierActivityList' => $carrierActivityList
        ];
//         $onlinePay = CarrierPayChannel::where('binded_third_part_pay_id','!=',null)->with('bindedThirdPartGateway.defPayChannel.payChannelType')->get();
//        if($item->bindedThirdPartGateway->defPayChannel->channel_code == PayChannel::GUOFUBAO && $item->bindedThirdPartGateway->defPayChannel->payChannelType->id == PayChannelType::ONLINE_PAY){
//            $payGateWay = new PayOrderRuntime(\WinwinAuth::memberUser(),$item,0);
//            $bankList = $payGateWay->bankList();
//            $item->bank_list = $bankList;
//        }

        if($payChannelTypeId == PayChannelType::ONLINE_PAY ) {
            $onlinePay = CarrierPayChannel::where('id', $carrierPayChannelId)->with('bindedThirdPartGateway.defPayChannel.payChannelType')->first();
            if($onlinePay->bindedThirdPartGateway->defPayChannel->channel_code == PayChannel::GUOFUBAO && $onlinePay->bindedThirdPartGateway->defPayChannel->payChannelType->id == PayChannelType::ONLINE_PAY){
                $payGateWay = new PayOrderRuntime(\WinwinAuth::memberUser(),$onlinePay,0);
                $bankList = $payGateWay->bankList();
                $onlinePay->bank_list = $bankList;
            }

            $online = [
                'onlinePay' => $onlinePay
            ];

            $online = array_merge($other, $online);
            return \WTemplate::onlineDeposit()->with($online);

        }elseif($payChannelTypeId == PayChannelType::SCAN_CODE_PAY ){

            return \WTemplate::scanCodeDeposit();

        }elseif($payChannelTypeId == PayChannelType::BANK_TRANSFER_PAY ){
            $bankTransfer = [
                'player' => $player,
                'bankList' => $bankList,
                'transferType' => PlayerDepositPayLog::onlineTransferType()
            ];
            $bankTransfer = array_merge($other, $bankTransfer);
            return \WTemplate::bankTransferDeposit()->with($bankTransfer);

        }elseif($payChannelTypeId == PayChannelType::SCAN_CODE_COMPANY_PAY){

            return \WTemplate::scanCodeCompanyDeposit()->with($other);

        }elseif($payChannelTypeId == PayChannelType::POINT_CARD_PAY ){

            return \WTemplate::pointCardDeposit()->with($other);

        }elseif($payChannelTypeId == PayChannelType::ONLINE_OR_SCAN_PAY ){

            //dd($other);
            return \WTemplate::onlineScanDeposit()->with($other);

        }

        return ;
    }


    /**
     * ajax搜索存款记录
     * @param Request $request
     * @return \View
     */
    public function depositRecords(Request $request)
    {
        //请求类型,默认是加载整个页面加数据, list(仅仅数据)
        $type = $request->get('type', '');
        $perPage = $request->get('perPage', 10);
        $start_time = $request->get('start_time', Carbon::now()->startOfMonth());
        $end_time = $request->get('end_time', Carbon::now()->endOfMonth());
        $status = $request->get('status', '');
        if(is_numeric($status) && !is_array($status)){
            $status = array($status);
        }else{
            $status = array_keys(PlayerDepositPayLog::orderStatusMeta());
        }
        if(empty($start_time)){
            $start_time = "2000-01-01 00:00:00";
        }
        if(empty($end_time)){
            $end_time = Carbon::now();
        }
        $playerDepositPaylog = PlayerDepositPayLog::where('player_id', \WinwinAuth::memberUser()->player_id)
            ->with('carrierPayChannel.payChannel.payChannelType')
            ->whereIn('status', $status)
            ->whereDate('created_at', '>=', $start_time)
            ->whereDate('created_at', '<=', $end_time)
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);

        $orderStatus = PlayerDepositPayLog::orderStatusMeta();
        if($request->ajax()){
            if($type){
                return \WTemplate::depositLists()->with('playerDepositPaylog', $playerDepositPaylog);
            }

            return \WTemplate::depositRecords()->with(['playerDepositPaylog'=>$playerDepositPaylog, 'orderStatus'=>$orderStatus]);
        }
    }

    /**
     * 删除存款记录
     * @param $pay_deposit_id
     * @return \Response
     * @throws \Exception
     */
    function depositRecordsDelete($pay_deposit_id){
        //$player_id = \WinwinAuth::memberUser()->player_id;
        $PlayerDepositPayLog = PlayerDepositPayLog::where('id', '=', $pay_deposit_id )->delete();
        if($PlayerDepositPayLog){
            return $this->sendSuccessResponse(route('players.depositRecords '));
        }else{
            return $this->sendErrorResponse('删除失败', 403);
        }
    }

    /**
     * 删除存款记录
     * @param $pay_deposit_id
     * @return \Response
     * @throws \Exception
     */
    function depositDropBatch(Request $request){
        if(empty($request->get('depositLogIdArr'))){
            return $this->sendErrorResponse('选择删除的记录', 403);
        }
        $PlayerDepositPayLog = PlayerDepositPayLog::whereIn('id', $request->get('depositLogIdArr') )->delete();
        if($PlayerDepositPayLog){
            return $this->sendSuccessResponse(route('players.depositRecords'));
        }else{
            return $this->sendErrorResponse('删除失败', 403);
        }
    }

    /**
     * 支付宝二维码界面
     * @param Request $request
     */
    public function CreateAlipayQRcode(Request $request){
        $pay_order_number = $request->get('pay_order_number');
        $pay_url = urldecode($request->get('pay_url'));
        $playerDepositPay = $this->depositPayLogRepository->findWhere(['pay_order_number'=>$pay_order_number], ['*'])->first();
        if($playerDepositPay) {
            return view('Web.default.players_center.alipay_scan', ['playerDepositPay'=>$playerDepositPay, 'pay_url'=>$pay_url]);
        }else {
            return $this->sendErrorResponse('订单不存在', 403);
        }
    }
}