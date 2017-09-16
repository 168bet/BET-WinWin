<?php

namespace App\Models\Def;

use App\Entities\CacheConstantPrefixDefine;
use App\Models\Map\CarrierGamePlat;
use App\Models\Map\CarrierGame;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Game
 *
 * @package App\Models
 * @version March 6, 2017, 8:19 am UTC
 * @property int $game_id
 * @property int $game_plat_id 所属游戏平台id
 * @property string $game_name 游戏名称
 * @property string $english_game_name 英文游戏名称
 * @property string $game_code 游戏代码
 * @property bool $status 状态 1正常  0关闭
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Def\GamePlat $gamePlat
 * @mixin \Eloquent
 * @property-read \App\Models\Map\CarrierGamePlat $carrierGamePlat
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Def\Game retrieveByEnglishGameName($gameName)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Def\Game open()
 * @property int $game_lines 游戏线路
 * @property string $game_icon_path 游戏图标路径

 */
class Game extends Model
{

    const STATUS_AVAILABLE = 1;
    const STATUS_CLOSED    = 0;

    public $table = 'def_games';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'game_id';


    public $fillable = [
        'game_plat_id',
        'game_name',
        'status',
        'game_code'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'game_id' => 'integer',
        'game_plat_id' => 'integer',
        'game_name' => 'string',
        'game_code' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function gamePlat()
    {
        return $this->belongsTo(GamePlat::class,'game_plat_id','game_plat_id');
    }

    public function carrierGamePlat(){
        return $this->belongsTo(CarrierGamePlat::class,'game_plat_id','game_plat_id');

    }

    public function scopeRetrieveByEnglishGameName(Builder $builder, $gameName){
        return $builder->where('english_game_name',$gameName);
    }

    public function scopeOpen(Builder $query){
        return $query->where('status' , self::STATUS_AVAILABLE);
    }

    public function scopeInIds(Builder $query, $ids){
        return $query->whereIn('game_id',$ids);
    }
    /**
     * 根据游戏名称获取游戏缓存信息
     * @param $gameName
     * @return Game
     */
    public static function getCachedGameByGameName($gameName){
        return \Cache::remember(CacheConstantPrefixDefine::GAME_INFO_BY_GAME_ENGLISH_NAME_PREFIX.$gameName,3600,function () use ($gameName){
            return Game::retrieveByEnglishGameName($gameName)->first();
        });
    }


    /**
     * 根据游戏代码获取游戏缓存信息
     * @param $gameCode
     * @return Game
     */
    public static function getCachedGameByGameCode($gameCode){
        return \Cache::remember(CacheConstantPrefixDefine::GAME_INFO_BY_GAME_CODE_PREFIX.$gameCode,3600,function () use ($gameCode){
            return Game::where('game_code',$gameCode)->with('gamePlat.mainGamePlat')->first();
        });
    }
}
