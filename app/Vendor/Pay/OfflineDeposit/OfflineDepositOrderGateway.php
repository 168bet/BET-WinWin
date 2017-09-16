<?php

namespace App\Vendor\Pay\OfflineDeposit;
use App\Helpers\IP\RealIpHelper;
use App\Models\CarrierPayChannel;
use App\Models\Log\PlayerDepositPayLog;
use App\Models\Player;
use App\Models\PlayerBankCard;
use App\Vendor\Pay\Exception\PayOrderRuntimeException;
use App\Vendor\Pay\Gateway\PayOrderAbstract;
use App\Vendor\Pay\Gateway\PayOrderFetchResponse;
use App\Vendor\Pay\Gateway\PayOrderInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\CarrierActivity;


/**
 * Created by PhpStorm.
 * User: wugang
 * Date: 2017/4/21
 * Time: 下午3:31
 */
class OfflineDepositOrderGateway extends PayOrderAbstract
{


    /**
     * 创建订单
     * @param Player $player
     * @param CarrierPayChannel $payChannel
     * @param float $amount
     * @return PayOrderFetchResponse
     */
    public function createOrder(Player $player, CarrierPayChannel $payChannel, $amount, PlayerBankCard $playerBankCard = null,$depositTime = null, $depositType = null){
        if(!$playerBankCard){
            throw new PayOrderRuntimeException('缺少会员转账银行卡');
        }
        if(!$depositTime) {
            throw new PayOrderRuntimeException('缺少转账时间');
        }
        try{
            Carbon::createFromFormat('Y-m-d H:i:s',$depositTime);
        }catch (\Exception $e){
            throw new PayOrderRuntimeException('缺少转账时间');
        }
        if(!$depositType || !in_array($depositType,[PlayerDepositPayLog::OFFLINE_TRANSFER_ATM,PlayerDepositPayLog::OFFLINE_TRANSFER_BANK])){
            throw new PayOrderRuntimeException('转账类型不合法');
        }
        //获取当日该用户的存款次数
        $count = PlayerDepositPayLog::byPlayerId($player->player_id)->where('created_at','>=',Carbon::now()->startOfDay()->toDateTimeString())->where('created_at','<=',Carbon::now()->endOfDay()->toDateTimeString())->count('*');
        if($payChannel->single_day_deposit_limit > 0 && $count >= $payChannel->single_day_deposit_limit){
            throw new PayOrderRuntimeException('超过当日存款次数');
        }
        //最大存款额
        if($payChannel->maximum_single_deposit > 0 && $amount > $payChannel->maximum_single_deposit){
            throw new PayOrderRuntimeException('超过最大存款额限制');
        }
        //最小存款额
        if($payChannel->single_deposit_minimum > 0 && $amount < $payChannel->single_deposit_minimum){
            throw new PayOrderRuntimeException('低于最小存款额限制');
        }
        $order = new PlayerDepositPayLog();
        $order->ip = RealIpHelper::getIp();
        $order->carrier_id = $player->carrier_id;
        $order->player_id  = $player->player_id;
        $order->pay_order_number = PlayerDepositPayLog::generatePayNumber();
        $order->carrier_pay_channel = $payChannel->id;
        $order->amount = $amount;
        $order->credential = PlayerDepositPayLog::generateCredential();
        $order->player_bank_card = $playerBankCard->card_id;
        $order->status = PlayerDepositPayLog::ORDER_STATUS_WAITING_REVIEW;
        $order->offline_transfer_deposit_at = $depositTime;
        $order->offline_transfer_deposit_type = $depositType;
        $order->save();
        $response = new PayOrderFetchResponse();
        $response->payOrder = $order;
        $response->payType = PayOrderFetchResponse::WEB_PAY_TYPE_OFF_LINE_TRANSFER;
        return $response;
    }

    /**
     * 检测订单是否合法
     * @param Request $request
     * @return \Response
     */
    public function verifyOrderIsLegal(Request $request){

    }



    public function getBankList()
    {
        // TODO: 从数据库查线下转账的银行卡列表
    }


}