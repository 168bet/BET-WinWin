<?php

namespace App\Models\Conf;

use App\Scopes\CarrierScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CarrierWebSiteConf
 *
 * @package App\Models\Carrier\Conf
 * @version March 13, 2017, 9:55 am UTC
 * @property int $id
 * @property int $carrier_id 所属运营商
 * @property string $site_title 网站标题
 * @property string $site_key_words 网站关键词
 * @property string $site_description 网站描述
 * @property string $site_javascript 网站js
 * @property string $site_notice 网站公告
 * @property string $site_footer_comment 网站底部说明
 * @property string $common_question_file_path 常见问题文件目录
 * @property string $contact_us_file_path 联系我们
 * @property string $privacy_policy_file_path 隐私政策文件目录
 * @property string $rule_clause_file_path 规则条款文件目录
 * @property string $with_draw_comment_file_path 提款说明文件目录
 * @property string $net_bank_deposit_comment 网银存款说明
 * @property string $atm_deposit_comment ATM存款说明
 * @property string $third_part_deposit_comment 第三方存款说明
 * @property string $commission_policy_file_path 佣金政策文件目录
 * @property string $jointly_operated_agreement_file_path 合营协议文件目录
 * @property string $activity_image_resolution 活动图片分辨率 按照*分隔  例如 1024*768
 * @mixin \Eloquent
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Conf\CarrierWebBannerConf[] $bannerImages
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereActivityImageResolution($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereAtmDepositComment($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereCarrierId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereCommissionPolicyFilePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereCommonQuestionFilePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereContactUsFilePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereJointlyOperatedAgreementFilePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereNetBankDepositComment($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf wherePrivacyPolicyFilePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereRuleClauseFilePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereSiteDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereSiteFooterComment($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereSiteJavascript($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereSiteKeyWords($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereSiteNotice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereSiteTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereThirdPartDepositComment($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conf\CarrierWebSiteConf whereWithDrawCommentFilePath($value)
 */
class CarrierWebSiteConf extends Model
{

    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new CarrierScope());
    }

    public $table = 'conf_carrier_web_site';

    /**
     *首页id
     */
    const SITE_INDEX_PAGE_ID = 1;
    /**
     *真人娱乐页面id
     */
    const SITE_FOR_ENJOY_PAGE_ID = 2;
    /**
     *彩票页面id
     */
    const SITE_LOTTERY_PAGE_ID = 3;
    /**
     *电子游戏页面id
     */
    const SITE_ELECTRONIC_GAME_PAGE_ID = 4;
    /**
     *体育游戏页面id
     */
    const SITE_SPORTS_GAME_PAGE_ID = 5;
    /**
     *优惠活动页面id
     */
    const SITE_PREFERENTIAL_ACTIVITIES_PAGE_ID = 6;
    /**
     *帮助页id
     */
    const SITE_HELP_PAGE_ID = 7;
    /**
     *合营代理页id
     */
    const SITE_JOINTLY_OPERATED_PAGE_ID = 8;

    public static function sitePages() {
        return [
            self::SITE_INDEX_PAGE_ID => '首页',
            self::SITE_FOR_ENJOY_PAGE_ID => '真人娱乐页',
            self::SITE_LOTTERY_PAGE_ID => '彩票页面',
            self::SITE_ELECTRONIC_GAME_PAGE_ID => '电子游戏页',
            self::SITE_SPORTS_GAME_PAGE_ID => '体育游戏页',
            self::SITE_PREFERENTIAL_ACTIVITIES_PAGE_ID => '优惠活动页',
            self::SITE_HELP_PAGE_ID => '帮助页',
            self::SITE_JOINTLY_OPERATED_PAGE_ID => '合营代理页'
        ];
    }

    /**
     *
     */
    const CREATED_AT = 'created_at';
    /**
     *
     */
    const UPDATED_AT = 'updated_at';


    /**
     * @var array
     */
    protected $dates = ['deleted_at'];


    /**
     * @var array
     */
    public $fillable = [
        'carrier_id',
        'site_title',
        'site_key_words',
        'site_description',
        'site_javascript',
        'site_notice',
        'site_footer_comment',
        'common_question_file_path',
        'contact_us_file_path',
        'privacy_policy_file_path',
        'rule_clause_file_path',
        'with_draw_comment_file_path',
        'net_bank_deposit_comment',
        'atm_deposit_comment',
        'third_part_deposit_comment',
        'commission_policy_file_path',
        'jointly_operated_agreement_file_path',
        'activity_image_resolution'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'carrier_id' => 'integer',
        'site_title' => 'string',
        'site_key_words' => 'string',
        'site_description' => 'string',
        'site_javascript' => 'string',
        'site_notice' => 'string',
        'site_footer_comment' => 'string',
        'common_question_file_path' => 'string',
        'contact_us_file_path' => 'string',
        'privacy_policy_file_path' => 'string',
        'rule_clause_file_path' => 'string',
        'with_draw_comment_file_path' => 'string',
        'net_bank_deposit_comment' => 'string',
        'atm_deposit_comment' => 'string',
        'third_part_deposit_comment' => 'string',
        'commission_policy_file_path' => 'string',
        'jointly_operated_agreement_file_path' => 'string'
    ];


    /**
     * 取款说明
     * @return null|string
     */
    public function with_draw_comment(){
        //如果存在这个文件目录且文件是存在的
        if($this->with_draw_comment_file_path && \Storage::disk('carrier')->exists($this->with_draw_comment_file_path)){
            //返回这个文件
            return \Storage::disk('carrier')->get($this->with_draw_comment_file_path);
        }
        return NULL;
    }

    /**
     * 佣金政策
     * @return null|string
     */
    public function commission_policy(){
        if($this->commission_policy_file_path && \Storage::disk('carrier')->exists($this->commission_policy_file_path)){
            return \Storage::disk('carrier')->get($this->commission_policy_file_path);
        }
        return NULL;
    }

    /**
     * 合营协议
     * @return null|string
     */
    public function jointly_operated_agreement(){
        if($this->jointly_operated_agreement_file_path && \Storage::disk('carrier')->exists($this->jointly_operated_agreement_file_path)){
            return \Storage::disk('carrier')->get($this->jointly_operated_agreement_file_path);
        }
        return NULL;
    }

    /**
     * 常见问题
     * @return null|string
     */
    public function common_question(){
        if($this->common_question_file_path && \Storage::disk('carrier')->exists($this->common_question_file_path)){
            return \Storage::disk('carrier')->get($this->common_question_file_path);
        }
        return NULL;
    }

    /**
     * 联系我们
     * @return null|string
     */
    public function contact_us(){
        if($this->contact_us_file_path && \Storage::disk('carrier')->exists($this->contact_us_file_path)){
            return \Storage::disk('carrier')->get($this->contact_us_file_path);
        }
        return NULL;
    }

    /**
     * 隐私政策
     * @return null|string
     */
    public function privacy_policy(){
        if($this->privacy_policy_file_path && \Storage::disk('carrier')->exists($this->privacy_policy_file_path)){
            return \Storage::disk('carrier')->get($this->privacy_policy_file_path);
        }
        return NULL;
    }

    /**
     * 规则条款
     * @return null|string
     */
    public function rule_clause(){
        if($this->rule_clause_file_path && \Storage::disk('carrier')->exists($this->rule_clause_file_path)){
            return \Storage::disk('carrier')->get($this->rule_clause_file_path);
        }
        return NULL;
    }

    public function bannerImages(){
        return $this->hasMany(CarrierWebBannerConf::class,'carrier_id','carrier_id');
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

        
    ];

    
}
