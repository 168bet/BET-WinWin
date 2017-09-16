<?php

namespace App\Repositories\Carrier;

use App\Models\Log\CarrierAgentWithdrawLog;
use InfyOm\Generator\Common\BaseRepository;

class CarrierAgentWithdrawLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'order_number',
        'carrier_id',
        'agent_id',
        'apply_amount',
        'fee_amount',
        'finally_withdraw_amount',
        'carrier_pay_channel',
        'player_bank_card',
        'status',
        'reviewed_at',
        'withdraw_succeed_at',
        'operator',
        'remark'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CarrierAgentWithdrawLog::class;
    }
}
