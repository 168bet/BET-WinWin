<?php

namespace App\DataTables\Carrier;

use App\Models\Map\CarrierGame;
use Yajra\Datatables\Services\DataTable;

class CarrierGameDataTable extends DataTable
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {

        return \Datatables::of($this->query())
            ->filter(function($instance){
                //获取gamePlatID执行游戏查询操作
                $instance->collection = $instance->collection->filter(function (CarrierGame $row) {
                    return $row->game->gamePlat->game_plat_id == $this->request()->get('gamePlatId');
                });
            })
            ->addColumn('action', 'Carrier.carrier_games.datatables_actions')
            //判断游戏是否开放
            ->addColumn('isOpen',function ($model){
                return $model->isOpen() ? '已开放' : '已关闭';
            })
            ->make(true);
    }

    /**
     * Get the query object to be processed by datatables.
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {

        $carrierGames = CarrierGame::with('game.gamePlat')->get();
        return $this->applyScopes($carrierGames);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\Datatables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->addAction(['width' => '10%','title' => '操作'])
            ->ajax([
                'data' => \Config::get('datatables.ajax.data'),
            ])
            ->parameters([
                'paging' => true,
                'searching' => false,
                'info' => true,
                'dom' => 'Bfrtipl',
                'scrollX' => false,
                'buttons' => [

                ],
                'language' => \Config::get('datatables.language'),
                'drawCallback' => 'function(){
                    var api = this.api();
                    var dataLists = api.data();
                    $("[name=\'winwin-switch\']").bootstrapSwitch({
                        size:\'mini\',
                        onText:\'正常\',
                        offText:\'关闭\',
                        on:\'success\',
                        off:\'warning\',
                        onSwitchChange:function(){
                            var tr = $(this).parents(\'tr\');
                            var data = dataLists[api.row(tr).index()]
                            data._method = \'PATCH\';
                            data.status  =  this.checked ? 1 : 0;
                            var serverUrl = \''.route('carrierGames.update',null).'\/\' + $(this).attr(\'data-id\')
                            $.fn.showOverlayLoading();
                            var _me = this;
                            $.ajax({
                                url:serverUrl,
                                data:data,
                                type:\'POST\',
                                success:function(e){
                                    if(e.success != true){
                                        toastr.error(e.message || \'更新失败\', \'出错啦!\')
                                    }
                                    $.fn.hideOverlayLoading();
                                },
                                error:function(xhr){
                                    $.fn.hideOverlayLoading();
                                    toastr.error(xhr.responseJSON.message || \'更新失败\', \'出错啦!\');
                                }
                            })
                        }
                    });
                }'
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    private function getColumns()
    {
        return [
            '游戏名称' => ['name' => 'game_id', 'data' => 'game.game_name','orderable' => false],
            '前台显示名称'=>['name'=>'diaplay_name','data'=>'display_name','orderable' => false],
            '排序' => ['name' => 'sort', 'data' => 'sort' ,'orderable' => false],
            '状态' => [
                'name' => 'status',
                'data' => 'status',
                'orderable' => false,
                'render' => 'function(){    
                 return "<input id=\'switch-animate\' data-id=\'"+ this.id +"\' name=\'winwin-switch\' type=\'checkbox\' "+ (this.status ?                  "checked=checked" : "") +">";
                }'],
            '是否已开放'=>[
                'name'=>'isOpen',
                'data'=>'isOpen',
                'orderable' => false
            ]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'carrierGames';
    }
}
