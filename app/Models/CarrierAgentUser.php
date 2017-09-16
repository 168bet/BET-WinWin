<?php

namespace App\Models;
use App\Exceptions\AgentAccountException;
use App\Models\Conf\CarrierDashLoginConf;
use App\Models\Conf\CarrierRebateFinancialFlowSubordinate;
use App\Models\Conf\CarrierSubordinateAgentCommission;
use App\Models\Conf\RebateFinancialFlowAgentGamePlatConf;
use App\Models\Log\AgentBearUndertakenLog;
use App\Traits\WinwinEntrustUserTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Auth;
use App\Scopes\CarrierScope;


/**
 * App\Models\CarrierAgentUser
 *
 * @property int $id
 * @property string $username 用户名
 * @property string $password 密码
 * @property string $realname 真实姓名
 * @property int $agent_level_id 代理层级ID
 * @property float $amount 代理余额
 * @property string $pay_password 取款密码
 * @property float $experience_amount 会员礼金
 * @property int $player_number 下线玩家数量
 * @property string $birthday 出生日期
 * @property string $skype skype账号
 * @property string $qq QQ
 * @property string $wechat 微信
 * @property string $mobile 手机号
 * @property string $email 邮箱
 * @property string $promotion_url 代理推广网址
 * @property int $promotion_url_click_number 代理推广网址点击次数
 * @property string $promotion_notion 代理推广介绍
 * @property string $promotion_code 推广码
 * @property int $parent_id 代理商父ID 介绍人
 * @property int $carrier_id 运营商ID
 * @property bool $status 代理商账号状态 1 启用 0, 禁用
 * @property bool $audit_status 客服审核状态 1已审核 =0审核中 2拒绝
 * @property bool $is_default 运营商默认代理 1是 0不是
 * @property string $customer_remark 客服备注
 * @property string $customer_time 客服处理时间
 * @property string $login_time 登录时间
 * @property string $register_ip 注册IP
 * @property \Carbon\Carbon $created_at 注册时间
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property string $remember_token
 * @property-read \App\Models\AgentBankCard $agentBankCard
 * @property-read \App\Models\CarrierAgentLevel $agentLevel
 * @property-read \App\Models\Conf\CarrierDashLoginConf $agentLoginConf
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Log\AgentBearUndertakenLog[] $bearUndertakeLogs
 * @property-read \App\Models\Carrier $carrier
 * @property-read \App\Models\CarrierAgentUser $parentAgent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Player[] $players
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Conf\RebateFinancialFlowAgentGamePlatConf[] $rebateFinancialFlowAgentGamePlatConf
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RolesModel\Role[] $roles
 * @property-read \App\Models\Conf\CarrierSubordinateAgentCommission $subordinateAgentCommission
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CarrierAgentUser active()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CarrierAgentUser orderByAgentUser($type)
 * @mixin \Eloquent
 */
class CarrierAgentUser extends Auth
{
    use WinwinEntrustUserTrait;

    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new CarrierScope());
    }

    public $table = 'inf_agent';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $hidden = ['password','pay_password','deleted_at'];

    /**
     * 权限认证类型
     * @var string
     */
    public $entrustType = 'agent';

    public $fillable = [
        'username',
        'password',
        'realname',
        'type',
        'agent_level_id',
        'amount',
        'birthday',
        'experience_amount',
        'player_number',
        'skype',
        'qq',
        'pay_password',
        'wechat',
        'mobile',
        'email',
        'promotion_url',
        'promotion_notion',
        'promotion_code',
        'parent_id',
        'carrier_id',
        'status',
        'audit_status',
        'customer_remark',
        'customer_time',
        'login_time',
        'register_ip',
        'player_rebate_financial_flow_rate',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'username' => 'string',
        'password' => 'string',
        'realname' => 'string',
        'agent_level_id' => 'integer',
        'player_number' => 'integer',
        'skype' => 'string',
        'qq' => 'string',
        'wechat' => 'string',
        'mobile' => 'string',
        'email' => 'string',
        'promotion_code' => 'string',
        'pay_password' => 'string',
        'promotion_description' => 'string',
        'parent_id' => 'integer',
        'carrier_id' => 'integer',
        'customer_remark' => 'string',
        'register_ip' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'username'    => 'required|max:50',
        'realname'    => 'required|max:50',
    ];

    public static $requestAttributes = [
        'username'=> '代理账号',
        'realname' => '姓名',
        'agent_level_id' => '代理类型',
        'password' => '密码',
        'birthday' => '出生日期',
        'skype' => 'skype账号',
        'qq' => 'QQ号码',
        'wechat' => '微信',
        'mobile' => '手机号码',
        'email' => '电子邮箱',
        'promotion_url' => '推广网址',
        'promotion_notion' => '推广介绍',
        'promotion_code' => '推广码',
        'confirm_password' => '确认密码',
        'commission_ratio' => '下级代理佣金提成比例'
    ];

    /**
     * 生成邀请码
     * @return string
     */
    public static function generateReferralCode(){
        $randStr = str_shuffle('abcdefghijklmnopqrstuvwxyz');
        $rand = substr($randStr,0,6);
        $result = self::where('promotion_code',$rand)->first();
        if ($result){
            return self::generateReferralCode();
        }
        return $rand;
    }

    public function scopeActive(Builder $query)
    {
        //有效且不能是默认运营商代理
        return $query->where('status', 1)->where('is_default', 0);
    }

    public function scopeOrderByAgentUser(Builder $query,$type){
        return $query->orderBy('status',$type);
    }



    //查找本身代理等级
    public static function getAgentLevel($parent_id){
        static $level = 1;
        $parentAgent = self::where('id',$parent_id)->first();
        if ($parentAgent){
            $level++;
            return self::getAgentLevel($parentAgent->parent_id);
        }
        return $level;
    }


    /**
     * 获取状态字典数据
     * 1已审核 =0审核中 2已拒绝',
     * @return array
     */
    public static function audit_statusMeta(){
        return [0 => '审核中', 1 => '已审核', 2 => '已拒绝'];
    }

    /**
     * 获取状态字典数据
     * 1 启用 0, 禁用
     * @return array
     */
    public static function statusMeta(){
        return [0 => '禁用', 1 => '启用'];
    }

    /**
     * @return bool
     */
    public function checkActive(){
        if($this->status == 0){
            throw new AgentAccountException('代理商账户被禁用');
        }
        return true;
    }


    /**
     * @return bool
     */
    public function isActive(){
        try{
            $this->checkActive();
        }catch (\Exception $e){
            throw $e;
        }
        return true;
    }


    /**
     * 是否是运营商默认代理
     * @return bool
     */
    public function isCarrierDefaultAgent(){
        return $this->agent_level_id == null || $this->is_default == true;
    }

    public function isCommissionAgent(){
        return $this->agentLevel && $this->agentLevel->isCommissionAgent();
    }

    public function isCostTakenAgent(){
        return $this->agentLevel && $this->agentLevel->isCostTakeAgent();
    }

    public function isRebateFinancialFlowAgent(){
        return $this->agentLevel && $this->agentLevel->isRebateFinancialFlowAgent();
    }

    /**
     * 关联代理类型数据
     */
    public function agentLevel(){
        return $this->hasOne(CarrierAgentLevel::class,'id','agent_level_id');
    }
    /**
     * 上级代理
     * @return type
     */
    public function parentAgent(){
        return $this->hasOne(CarrierAgentUser::class,'id','parent_id');
    }


    /**
     * 代理未结算的洗码承担总额
     * @param Builder $query
     * @return mixed
     */
    public function unSettledRebateFinancialFlowAmount(){
        return AgentBearUndertakenLog::isSettled(false)->undertakeType(AgentBearUndertakenLog::UNDERTAKEN_TYPE_BET_FINANCIAL_FLOW)->byAgentUser($this->id)->sum('amount');
    }

    //下级洗码代理游戏平台洗码比例
    public function rebateFinancialFlowSubordinate(){
        return $this->hasMany(CarrierRebateFinancialFlowSubordinate::class,'agent_id','id');
    }


    public function rebateFinancialFlowAgentGamePlatConf(){
        return $this->hasMany(RebateFinancialFlowAgentGamePlatConf::class,'agent_id','id');
    }

    public function agentLoginConf(){
        return $this->belongsTo(CarrierDashLoginConf::class,'carrier_id','carrier_id');
    }

    public function subordinateAgentCommission(){
        return $this->hasOne(CarrierSubordinateAgentCommission::class,'agent_id','id');
    }


    /**
     * 承担的玩家成本记录
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bearUndertakeLogs(){
        return $this->hasMany(AgentBearUndertakenLog::class,'id','agent_id');
    }

    public static function createRules($current_carrier_id){
        return array_merge(self::$rules,[
            'username' => 'required|max:50|unique:inf_agent,username,NULL,id,carrier_id,'.$current_carrier_id,
            'realname' => 'required|max:50|unique:inf_agent,realname,NULL,id,carrier_id,'.$current_carrier_id,
        ]);
    }

    public static function updateRules($current_carrier_id,$except_id){
        return array_merge(self::$rules,[
            'username' => 'required|max:50|unique:inf_agent,username,'.$except_id.',id,carrier_id,'.$current_carrier_id,
            'realname' => 'required|max:50|unique:inf_agent,realname,'.$except_id.',id,carrier_id,'.$current_carrier_id,
        ]);
    }
    
    /**
     * 所属运营商
     * @return mixed
     */
    public function carrier(){
        return $this->belongsTo(Carrier::class,'carrier_id','id');
    }
    
    /**
     * 代理旗下的会员
     * @return type
     */
    public function players()
    {
        return $this->hasMany(Player::class,'agent_id','id');
    }

    /*
     * 代理取款银行卡信息
     */
    public function agentBankCard(){
        return $this->hasOne(AgentBankCard::class,'agent_id','id');
    }

}
