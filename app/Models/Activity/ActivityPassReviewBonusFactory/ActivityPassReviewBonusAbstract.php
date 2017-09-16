<?php
/**
 * Created by PhpStorm.
 * User: wugang
 * Date: 2017/4/25
 * Time: 下午5:42
 */

namespace App\Models\Activity\ActivityPassReviewBonusFactory;


use App\Models\Activity\CarrierActivityFlowLimitedPlatform;
use App\Models\CarrierActivity;
use App\Models\CarrierActivityAudit;
use App\Models\Log\AgentBearUndertakenLog;
use App\Models\Log\CarrierQuotaConsumptionLog;
use App\Models\Log\PlayerAccountLog;
use App\Models\Log\PlayerDepositPayLog;
use App\Models\Log\PlayerWithdrawFlowLimitLog;
use App\Models\Log\PlayerWithdrawFlowLimitLogGamePlat;
use App\Models\Player;
use Illuminate\Support\Collection;

abstract class ActivityPassReviewBonusAbstract
{

    const AUDIT_MANUAL = 1;
    const AUDIT_AUTO   = 2;

    /**
     * @var CarrierActivity
     */
    protected $carrierActivity;


    /**
     * @var CarrierActivityAudit
     */
    protected $carrierActivityAudit;

    public $audit_type = self::AUDIT_MANUAL;

    /**
     * @var PlayerDepositPayLog
     */
    protected $recentlyDepositPayLog;


    /**
     * @var PlayerAccountLog
     */
    protected $playerAccountLog;

    public function __construct(CarrierActivityAudit &$carrierActivityAudit){
        $this->carrierActivity = $carrierActivityAudit->activity;
        $this->carrierActivityAudit = $carrierActivityAudit;
    }


    /**
     * 处理红利, 流水限制数据
     * @param Player $player
     * @return mixed
     */
    abstract public function handleBonus(Player $player);


    /**
     * 获取红利和取款流水限制
     * @param Player $player
     * @return mixed
     */
    abstract public function getBonusAndWithdrawLimitFlow(Player $player);
    /**
     * 最近一次已完成的存款额
     * @param Player $player
     * @return PlayerDepositPayLog|null
     */
    protected function getRecentlyDepositLog(Player $player){
        if(!$this->recentlyDepositPayLog){
            $this->recentlyDepositPayLog = PlayerDepositPayLog::where('player_id',$player->id)->payedSuccessfully()->orderBy('created_at','desc')->first();
        }
        return $this->recentlyDepositPayLog;
    }


    /**
     * 根据活动规则生成取款流水限制金额
     * @param integer $flowLimitTime
     * @param float $depositAmount 存款额
     * @param float $bonus 红利
     * @return float
     */
    protected function getWithdrawLimitAmount($flowLimitTime,$depositAmount, $bonus){
        $flowPattern = $this->carrierActivity->flow_want_pattern;
        $withdrawLimitAmount = 0;
        switch ($flowPattern){
            case CarrierActivity::FLOW_WANT_PATTERN_BONUS:
                $withdrawLimitAmount = $flowLimitTime * $bonus;
                break;
            case CarrierActivity::FLOW_WANT_PATTERN_BONUS_DEPOSIT:
                $withdrawLimitAmount = $flowLimitTime * ($bonus + $depositAmount);
                break;
            case CarrierActivity::FLOW_WANT_PATTERN_DEPOSIT:
                $withdrawLimitAmount = $flowLimitTime * $depositAmount;
        }
        return $withdrawLimitAmount;
    }


    /**
     * 修改最近一次存款记录的红利金额
     * @param Player $player
     * @param $bonusAmount
     */
    public function modifyDepositLogBonus(Player $player, $bonusAmount){
        $log = $this->getRecentlyDepositLog($player);
        if($log){
            $log->bonus_amount += $bonusAmount;
            $log->update();
        }
    }


    /**
     * 新增会员红利数据
     * @param Player $player
     * @param $bonusAmount
     */
    public  function newBonusRecord(Player $player, $bonusAmount){
        //新增玩家主账户余额
        $player->main_account_amount += $bonusAmount;
        $player->update();
        //新增玩家资金流水
        $this->playerAccountLog = new PlayerAccountLog();
        $this->playerAccountLog->fund_type = PlayerAccountLog::FUND_TYPE_BONUS;
        $this->playerAccountLog->fund_source = $this->audit_type == self::AUDIT_MANUAL ? '人工审核' : '系统自动审核';
        $this->playerAccountLog->carrier_id = $player->carrier_id;
        $this->playerAccountLog->player_id = $player->player_id;
        $this->playerAccountLog->amount = $bonusAmount;
        $this->playerAccountLog->operator_reviewer_id = $this->audit_type == self::AUDIT_MANUAL ? \WinwinAuth::carrierUser()->id : null;
        $this->playerAccountLog->save();
        //判断红利承担方
        $agent = $player->agent;
        //如果有代理,并且是佣金代理, 那么佣金代理需要承担红利成本
        $carrierUnderTake = new CarrierQuotaConsumptionLog();
        $carrierUnderTake->carrier_id = $player->carrier_id;
        if($agent && $agent->isCommissionAgent()){
            $conf = $agent->agentLevel->commissionAgentConf;
            $calculateAgentBonusUnderTakeAmount = $bonusAmount * $conf->bonus_undertake_ratio * 0.01;
            $bonusUnderTakeMax = $conf->bonus_undertake_max;
            $bonusUnderTakeAmount = $bonusUnderTakeMax > 0 ? ( $calculateAgentBonusUnderTakeAmount > $bonusUnderTakeMax ? $bonusUnderTakeMax : $calculateAgentBonusUnderTakeAmount) : $calculateAgentBonusUnderTakeAmount;
            $carrierBonusUnderTake = $calculateAgentBonusUnderTakeAmount - $bonusUnderTakeMax;
            $carrierBonusUnderTake = $bonusUnderTakeMax > 0 ? ( $carrierBonusUnderTake > 0 ? $carrierBonusUnderTake : 0 ) : 0;
            //代理承担的红利成本
            if($bonusUnderTakeAmount > 0){
                $agentUnderTakeLog = new AgentBearUndertakenLog();
                $agentUnderTakeLog->amount = $bonusUnderTakeAmount;
                $agentUnderTakeLog->agent_id = $agent->id;
                $agentUnderTakeLog->carrier_id = $agent->carrier_id;
                $agentUnderTakeLog->undertaken_type = AgentBearUndertakenLog::UNDERTAKEN_TYPE_BONUS;
                $agentUnderTakeLog->save();
            }
            //如果超过代理最大承担成本, 那么需要运营商承担这部分成本
            if($carrierBonusUnderTake){
                $carrierUnderTake->amount = -$carrierUnderTake;
                $carrierUnderTake->consumption_source = '代理红利成本超过上限,此部分由运营商承担;';
                $carrierUnderTake->save();
            }
        }
        //否则需要运营商承担成本
        else{
            $carrierUnderTake->amount = -$bonusAmount;
            $carrierUnderTake->consumption_source = '会员红利发放';
            $carrierUnderTake->save();
        }


    }

    //更新活动参与记录
    public function updateCarrierActivityJoinTimes(Player $player,$bonusAmount){
        $this->carrierActivity->join_times += 1;
        $doesNotJoinActivity = CarrierActivityAudit::where('player_id', $player->player_id)->byActivity($this->carrierActivity->id)->count() == 1;
        //如果只有一条申请记录,说明之前没有参与过
        if($doesNotJoinActivity){
            $this->carrierActivity->join_player_count += 1;
        }
        $log = $this->getRecentlyDepositLog($player);
        if($log){
            $this->carrierActivity->join_deposit_amount +=  $log->amount;
            $this->carrierActivity->join_bonus_amount += $bonusAmount;
            $this->carrierActivityAudit->process_bonus_amount += $bonusAmount;
            $this->carrierActivityAudit->process_deposit_amount = $log->amount;
            $this->carrierActivity->update();
        }
    }


    /**
     * 新增取款流水限制数据
     * @param Player $player
     * @param $flowLimitAmount
     * @param Collection[] $gamePlats
     */
    public function newWithdrawLimitLog(Player $player, $flowLimitAmount, Collection $gamePlats = null){
        $withdrawFlowLimitLog = new PlayerWithdrawFlowLimitLog();
        $withdrawFlowLimitLog->carrier_id = $player->carrier_id;
        $withdrawFlowLimitLog->player_id = $player->player_id;
        $withdrawFlowLimitLog->limit_amount = $flowLimitAmount;
        $this->carrierActivityAudit->process_withdraw_flow_limit += $flowLimitAmount;
        $withdrawFlowLimitLog->related_activity = $this->carrierActivity->id;
        $withdrawFlowLimitLog->limit_type = PlayerWithdrawFlowLimitLog::LIMIT_TYPE_BENEFIT_ACTIVITY;
        $withdrawFlowLimitLog->operator_id =  $this->audit_type == self::AUDIT_MANUAL ? \WinwinAuth::carrierUser()->id : null;
        $withdrawFlowLimitLog->player_account_log = $this->playerAccountLog->log_id;
        $withdrawFlowLimitLog->limit_amount > 0 && $withdrawFlowLimitLog->save();
        if($gamePlats){
            $gamePlats->each(function(CarrierActivityFlowLimitedPlatform $element) use ($withdrawFlowLimitLog){
                $gamePlat = new PlayerWithdrawFlowLimitLogGamePlat();
                $gamePlat->def_game_plat_id = $element->carrier_game_plat_id;
                $gamePlat->player_withdraw_flow_limit_id = $withdrawFlowLimitLog->id;
                $gamePlat->save();
            });
        }
    }

}