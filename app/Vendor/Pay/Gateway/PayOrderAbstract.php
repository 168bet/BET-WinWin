<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/5 0005
 * Time: 下午 8:17
 */

namespace App\Vendor\Pay\Gateway;


use App\Models\Log\PlayerDepositPayLog;

abstract class PayOrderAbstract implements PayOrderInterface
{

    /**
     * @var PayOrderFetchResponse
     */
    protected $orderFetchResponse;

    /**
     * @var PlayerDepositPayLog
     */
    public $playerDepositPayOrder;


    public function applyCustomPayCondition(\Closure $callable = null)
    {
        if($callable){
            $callable($this);
        }
    }


    public function getDepositPayLogWhenVerifySuccess()
    {
        return $this->playerDepositPayOrder;
    }

}