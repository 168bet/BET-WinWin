<?php
/**
 * 游戏接口会员记录搜索条件
 * Created by PhpStorm.
 * User: wugang
 * Date: 17/3/10
 * Time: 下午5:05
 */

namespace App\Vendor\GameGateway\Gateway;


class GameGatewaySearchCondition
{


    /**
     * @var
     */
    public $start_date;

    /**
     * @var
     */
    public $end_date;

    public function __construct()
    {
        $this->start_date = date('Y-m-d H:i:s',strtotime('-1 day'));
        //修复为获取三分钟之前的数据
        $this->end_date   = date('Y-m-d H:i:s',strtotime('-3 minute'));
    }

}