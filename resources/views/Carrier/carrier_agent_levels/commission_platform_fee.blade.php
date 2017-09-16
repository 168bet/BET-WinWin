<div class="modal-dialog" role="document" style="width: 1000px;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle">佣金方案</h4>
        </div>
        <div class="modal-header">
            <span style="color: red;">
                注意：如选择《按佣金计算》，则洗码设置不生效，反之选择《按有效投注额计算》，则平台费设置不生效</br>
                1、佣金计算方式：公司总输赢 减去 平台费、存款手续费、存款优惠、红利、返水等占用成本 等于 净输值 乘以 佣金比例</br>
                2、有效投注额计算方式：有效投注额 乘以 洗码比例，如会员产生有效投注额一万，洗码比例设置为 0.1% ，代理可获得10元
            </span>
        </div>
        {!! Form::model($carrierAgentLevel, ['route' => ['carrierAgentLevels.savePlatformFee', $carrierAgentLevel->id], 'method' => 'patch']) !!}
        <div class="modal-body" id="modalContent">
            <div class="row">
                <div class="box-body">
                    <div class="col-sm-12">
                        <table class="table table-bordered table-hover dataTable text-center">
                            <thead>
                                <tr role="row">
                                    <th>游戏平台</th>
                                    <th>
                                        计算方式
                                        <i class="fa fa-question-circle" style="color: #f44336"  data-toggle="tooltip" data-original-title="如选择《按佣金计算》，则洗码设置不生效，反之选择《按有效投注额计算》，则平台费设置不生效" ></i>
                                    </th>
                                    <th>
                                        平台费比例%
                                        <i class="fa fa-question-circle" style="color: #f44336"  data-toggle="tooltip" data-original-title="比例通常设置为10%-17%之间" ></i>
                                    </th>
                                    <th>
                                        平台费上限
                                        <i class="fa fa-question-circle" style="color: #f44336"  data-toggle="tooltip" data-original-title="平台费上限，填0为不设上限" ></i>
                                    </th>
                                    <th>
                                        洗码比例%
                                        <i class="fa fa-question-circle" style="color: #f44336"  data-toggle="tooltip" data-original-title="比例通常设置为0.1%-1%之间" ></i>
                                    </th>
                                    <th>
                                        洗码上限
                                        <i class="fa fa-question-circle" style="color: #f44336"  data-toggle="tooltip" data-original-title="洗码上限，填0为不设上限" ></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agentpa as $key=>$rebateFinancialFlow)
                                    <tr role="row">
                                        
                                        <td>
                                            {!! $rebateFinancialFlow->carrierGamePlat->gamePlat->game_plat_name !!}
                                            <input type="hidden" name="setid[{!! $key !!}]" value="{!! $rebateFinancialFlow->id !!}">
                                        </td>
                                        <td>
                                            <?php $computingModeDic = \App\Models\Conf\CarrierCommissionAgentPlatformFee::computingModeMeta() ?>
                                            <select name="computing_mode[{!! $key !!}]" id="computing_mode" class="form-control disable_search_select2 som_mode" style="width: 100%;">
                                                @foreach($computingModeDic as $k => $value)
                                                    @if($rebateFinancialFlow->computing_mode == $k)
                                                        <option value="{!! $k !!}" selected>{!! $value !!}</option>
                                                    @else
                                                        <option value="{!! $k !!}">{!! $value !!}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="platform_fee_rate[{!! $key !!}]" value="{!! $rebateFinancialFlow->platform_fee_rate !!}" class="form-control">
                                        </td>
                                        <td>
                                            <input type="text" name="platform_fee_max[{!! $key !!}]" value="{!! $rebateFinancialFlow->platform_fee_max !!}" class="form-control">
                                        </td>
                                        <td>
                                            <input type="number" name="agent_rebate_financial_flow_rate[{!! $key !!}]" value="{!! $rebateFinancialFlow->agent_rebate_financial_flow_rate !!}" class="form-control">
                                        </td>
                                        <td>
                                            <input type="text" name="agent_rebate_financial_flow_max_amount[{!! $key !!}]" value="{!! $rebateFinancialFlow->agent_rebate_financial_flow_max_amount !!}" class="form-control">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
       <div class="modal-footer">
            <div class="form-group col-sm-12">
                {!! TableScript::editFormSubmitAndCancelButtonsScript(Route('carrierAgentLevels.savePlatformFee',$carrierAgentLevel->id)) !!}
            </div>
        </div>
        {!! Form::close() !!}

    </div>
</div>
<script>
$(function () {
    $('.disable_search_select2').select2({
        minimumResultsForSearch: Infinity
    });
})
</script>