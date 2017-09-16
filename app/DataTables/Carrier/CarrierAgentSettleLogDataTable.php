<?php

namespace App\DataTables\Carrier;
use Form;
use Yajra\Datatables\Services\DataTable;
use App\Models\Log\CarrierAgentSettleLog;
class CarrierAgentSettleLogDataTable extends DataTable
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        return $this->datatables
            ->eloquent($this->query())
            ->addColumn('action', 'Carrier.carrier_agent_commission_settle_logs.datatables_actions')
            ->addColumn('status_name',function(CarrierAgentSettleLog $log){
                return CarrierAgentSettleLog::statusMeta()[$log->status];
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
        $carrierActivityAudit = CarrierAgentSettleLog::with(['agent.agentLevel','settlePeriods'])->where('status','!=',CarrierAgentSettleLog::SET_COMPLETED_STATUS);
        return $this->applyScopes($carrierActivityAudit);
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
                'data' => \Config::get('datatables.ajax.data')
            ])
            ->parameters([
                'paging' => true,
                'searching' => false,
                'ordering' => false,
                'info' => true,
                'dom' => 'Bfrtipl',
                'scrollX' => false,
                'buttons' => [
                ],
                'language' => \Config::get('datatables.language'),
                'drawCallback' => 'function(){
                    var api = this.api();
                    var startIndex= api.context[0]._iDisplayStart;
                    api.column(0).nodes().each(function(cell, i) {
                        cell.innerHTML = startIndex + i + 1;
                });
                
                $(".user_edit").on("click",function(){
                    $.fn.overlayToggle();
                    var _me = this;
                    var user_id = $(_me).attr("data-id")
                    $.fn.winwinAjax.buttonActionSendAjax(_me,"'.route('carrierAgentSettleLogs.gamePlatWinAmount',null).'/"+ user_id,{},function(content){
                        $.fn.overlayToggle();
                        $("#userInfoEditModal").html(content);
                        $("#userInfoEditModal").modal("show");
                    },function(){

                    },"GET",{dataType:"html"})
                })
                
                $(".cost_share_edit").on("click",function(){
                    $.fn.overlayToggle();
                    var _me = this;
                    var user_id = $(_me).attr("data-id")
                    $.fn.winwinAjax.buttonActionSendAjax(_me,"'.route('carrierAgentSettleLogs.costShare',null).'/"+ user_id,{},function(content){
                        $.fn.overlayToggle();
                        $("#userInfoEditModal").html(content);
                        $("#userInfoEditModal").modal("show");
                    },function(){

                    },"GET",{dataType:"html"})
                })
                
                $(".manual_tuneup_edit").on("click",function(){
                    $.fn.overlayToggle();
                    var _me = this;
                    var user_id = $(_me).attr("data-id")
                    $.fn.winwinAjax.buttonActionSendAjax(_me,"'.route('carrierAgentSettleLogs.manualTuneup',null).'/"+ user_id,{},function(content){
                        $.fn.overlayToggle();
                        $("#userInfoEditModal").html(content);
                        $("#userInfoEditModal").modal("show");
                    },function(){

                    },"GET",{dataType:"html"})
                })
                
                $(".actual_payment_edit").on("click",function(){
                    $.fn.overlayToggle();
                    var _me = this;
                    var user_id = $(_me).attr("data-id")
                    $.fn.winwinAjax.buttonActionSendAjax(_me,"'.route('carrierAgentSettleLogs.actualPayment',null).'/"+ user_id,{},function(content){
                        $.fn.overlayToggle();
                        $("#userInfoEditModal").html(content);
                        $("#userInfoEditModal").modal("show");
                    },function(){

                    },"GET",{dataType:"html"})
                })

                }']);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    private function getColumns()
    {
        return [
            '序号' => ['data' => 'id'],
            '结算期' => ['data' => 'settle_periods.periods','defaultContent' => '','orderable' => false],
            '代理账号' => ['data' => 'agent.username','defaultContent' => '','orderable' => false],
            '代理名称' => ['data' => 'agent.agent_level.level_name','defaultContent' => '','orderable' => false],
            '有效会员' => ['name'=>'available_member_number','data' => 'available_member_number','defaultContent' => '','orderable' => false],
//            '公司输赢' => ['data' => 'game_plat_win_amount','defaultContent' => '0.00','orderable' => false,'render' => 'function(){
//                      return "<a class=\"text-primary user_edit\" data-id=\""+ (this.id ? this.id : null) +"\" style=\"cursor: pointer\">" + (this.game_plat_win_amount ? this.game_plat_win_amount : 0.00) + "</a>"
//            }'],
            '公司输赢' => ['data' => 'game_plat_win_amount','defaultContent' => '0.00','orderable' => false],
            '成本分摊' => ['data' => 'cost_share','defaultContent' => '0.00','orderable' => false,'render' => 'function(){
                      return "<a class=\"text-primary cost_share_edit\" data-id=\""+ (this.id ? this.id : null) +"\" style=\"cursor: pointer\">" + (this.cost_share ? this.cost_share : 0.00) + "</a>"
            }'],
            '累加上月' => ['data' => 'cumulative_last_month','defaultContent' => '0.00','orderable' => false],
            '手工调整' => ['data' => 'manual_tuneup','defaultContent' => '0.00','orderable' => false,'render' => 'function(){
                      return "<a class=\"text-primary manual_tuneup_edit\" data-id=\""+ (this.id ? this.id : null) +"\" style=\"cursor: pointer\">" + (this.manual_tuneup ? this.manual_tuneup : 0.00) + "</a>"
            }'],
            '本期佣金' => ['data' => 'this_period_commission','defaultContent' => '0.00','orderable' => false],
            '实际发放' => ['data' => 'actual_payment','defaultContent' => '0.00','orderable' => false,'render' => 'function(){
                      return "<a class=\"text-primary actual_payment_edit\" data-id=\""+ (this.id ? this.id : null) +"\" style=\"cursor: pointer\">" + (this.actual_payment ? this.actual_payment : 0.00) + "</a>"
            }'],
            '结转下月' => ['data' => 'transfer_next_month','defaultContent' => '0.00','orderable' => false],
            '审核状态' => ['data' => 'status_name','defaultContent' => '','orderable' => false],
            '备注' => ['data' => '','defaultContent' => '','orderable' => false],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'carrierActivityTypes';
    }
}
