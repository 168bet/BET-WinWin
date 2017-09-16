<?php

namespace App\Models\Log;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\CarrierScope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Class CarrierAgentDepositPayLog
 * @package App\Models\Carrier
 * @version April 25, 2017, 1:20 pm CST
 */
class CarrierAgentDepositPayLog extends Model
{

    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new CarrierScope());
    }
    
    public $table = 'log_agent_deposit_pay';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     *订单已创建
     */
    const ORDER_STATUS_CREATED = 0;
    /**
     *订单支付成功
     */
    const ORDER_STATUS_PAY_SUCCEED = 1;
    /**
     *订单待审核
     */
    const ORDER_STATUS_WAITING_REVIEW = 2;
    /**
     *订单支付失败
     */
    const ORDER_STATUS_PAY_FAILED  = -1;
    /**
     *订单未审核通过
     */
    const ORDER_STATUS_SERVER_REVIEW_NO_PASSED = -2;

    /**
     *线下ATM转账
     */
    const OFFLINE_TRANSFER_ATM = 1;
    /**
     *线下银行转账
     */
    const OFFLINE_TRANSFER_BANK = 2;
    
    protected $dates = ['deleted_at'];


    public $fillable = [
        'pay_order_number',
        'carrier_id',
        'pay_order_channel_trade_number',
        'agent_id',
        'amount',
        'finally_amount',
        'benefit_amount',
        'bonus_amount',
        'fee_amount',
        'pay_channel',
        'status',
        'review_user_id',
        'operate_time',
        'credential',
        'remark'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'pay_order_number' => 'string',
        'carrier_id' => 'integer',
        'pay_order_channel_trade_number' => 'string',
        'agent_id' => 'integer',
        'pay_channel' => 'integer',
        'review_user_id' => 'integer',
        'credential' => 'string',
        'remark' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public static $requestAttributes = [
        'amount' => '金额'
    ];

    public static function createRules(){
        return [
            'amount' => 'required|numeric|min:0'
        ];
    }

    public static function onlineTransferType(){
        return [
            self::OFFLINE_TRANSFER_ATM  => 'ATM转账',
            self::OFFLINE_TRANSFER_BANK => '银行转账'
        ];

    }



    public static function orderStatusMeta(){
        return [
            self::ORDER_STATUS_CREATED => '订单创建',
            self::ORDER_STATUS_PAY_SUCCEED => '支付成功',
            self::ORDER_STATUS_PAY_FAILED => '支付失败',
            self::ORDER_STATUS_WAITING_REVIEW => '待审核',
            self::ORDER_STATUS_SERVER_REVIEW_NO_PASSED => '审核未通过'
        ];
    }


    /**
     * 订单是否能够支付
     * @return bool
     */
    public function canPay(){
        if($this->player->isActive() == false || $this->player->isLocked() == true){
            return false;
        }
        return $this->status == self::ORDER_STATUS_CREATED;
    }


    /**
     * 订单是否支付成功
     * @return bool
     */
    public function isPayedSuccessfully(){
        if($this->player->isActive() == false || $this->player->isLocked() == true){
            return false;
        }
        return $this->status == self::ORDER_STATUS_PAY_SUCCEED || $this->status == self::ORDER_STATUS_WAITING_REVIEW;
    }


    /**
     * 根据订单号查询订单
     * @param Builder $query
     * @param $orderNumber
     * @return Builder
     */
    public function scopeRetrieveByOrderNumber(Builder $query, $orderNumber){
        return $query->where('pay_order_number',$orderNumber);
    }

    public function scopePayedSuccessfully(Builder $query){
        return $query->where('status', self::ORDER_STATUS_PAY_SUCCEED);
    }

    public function scopeWaitingReview(Builder $query){
        return $query->where('status',self::ORDER_STATUS_WAITING_REVIEW);
    }

    public function scopeByPlayerId(Builder $query, $playerId){
        return $query->where('player_id',$playerId);
    }

    public function scopeByFinishTimeRange(Builder $query, $startTime, $endTime){
        return $query->whereBetween('operate_time',[$startTime,$endTime]);
    }

    public function scopeOrderByFinishTime(Builder $query, $orderType){
        return $query->orderBy('operate_time',$orderType);
    }


    public function scopeBetween(Builder $query, $start_time, $end_time){
        return $query->whereBetween('created_at', [$start_time, $end_time]);
    }

    /**
     * 生成订单号
     * @return string
     * @throws \Exception
     */
    public static function generatePayNumber(){
        try{
            DB::beginTransaction();
            do{
                $payNumber = time().rand(100000,999999);
                //悲观锁应用
            }while(PlayerDepositPayLog::lockForUpdate()->where('pay_order_number',$payNumber)->count() > 0);
            DB::commit();
            return $payNumber;
        }catch (\Exception $e){
            throw $e;
        }
    }


    /**
     * 生成凭证
     * @return string
     * @throws \Exception
     */
    public static function generateCredential(){
        try{
            DB::beginTransaction();
            do{
                $credential = substr(md5(time().rand(100000,999999)),0,6);
            }while(PlayerDepositPayLog::lockForUpdate()->where('credential',$credential)->count() > 0);
            DB::commit();
            return $credential;
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * 订单是否能够审核
     * @return bool
     */
    public function canReview(){
        if($this->player->isActive() == false || $this->player->isLocked() == true){
            return false;
        }
        return $this->status == self::ORDER_STATUS_WAITING_REVIEW;
    }
    
    public function carrierPayChannel(){
        return $this->belongsTo(\App\Models\CarrierPayChannel::class,'carrier_pay_channel','id');
    }
    
    public function reviewUser(){
        return $this->hasOne(\App\Models\CarrierUser::class,'id','review_user_id');
    }
    
    public function agent(){
        return $this->belongsTo(\App\Models\CarrierAgentUser::class,'id','agent_id');
    }

}
