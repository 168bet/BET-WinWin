@section('css')
    @include('Carrier.layouts.datatables_css')
    <link rel="stylesheet" href="{!! asset('daterangepicker/daterangepicker.css') !!}">
    <link rel="stylesheet" href="{!! asset('datepicker/datepicker3.css') !!}">
@endsection

<?php
$payChannelTypes = \App\Models\Def\PayChannelType::topPayChannelTypes();
?>

<div class="col-md-12">
    <form action="" id="searchForm">
        <div class="col-md-4">
            <div class="input-group">
                <div class="input-group-addon">
                    <span>时间</span>
                </div>
                <input type="text" name="date_time_range" class="form-control pull-right" id="reservationtime">
            </div>
        </div>
        {{--<div class="col-md-2">--}}
            {{--<div class="input-group">--}}
                {{--<div class="input-group-addon">--}}
                    {{--<span>支付渠道</span>--}}
                {{--</div>--}}
                {{--<select name="top_pay_channel" class="form-control disable_search_select2" id="">--}}
                    {{--<option value="">--请选择---</option>--}}
                    {{--@foreach($payChannelTypes as $payChannelType)--}}
                        {{--<option value="{!! $payChannelType->id !!}">{!! $payChannelType->type_name !!}</option>--}}
                    {{--@endforeach--}}
                {{--</select>--}}
            {{--</div>--}}
        {{--</div>--}}
        <div class="col-md-2">
            <div class="input-group">
                <div class="input-group-addon">
                    <span>搜索</span>
                </div>
                <input type="text" name="search[value]" class="form-control pull-right" id="reservationtime">
                <input type="hidden" name="search[regex]" value="false">
            </div>
        </div>
        <div class="col-md-1">
            <div class="input-group input-group-sm">
                <button class="btn btn-primary btn-md" type="submit">搜索</button>
            </div>
        </div>
    </form>
</div>


<div class="col-md-12">
    {!! $dataTable->table(['width' => '100%','class' => 'table table-bordered table-hover dataTable','style' => 'text-align:center']) !!}
</div>

@section('scripts')
    <script src="{!!  asset('js/vue.min.js') !!}"></script>
    @include('Carrier.layouts.datatables_js')
    {!! $dataTable->scripts() !!}
    @include('vendor.datatable.datatables_template')
    <script src="{!! asset('daterangepicker/moment.min.js') !!}"></script>
    <script src="{!! asset('daterangepicker/daterangepicker.js') !!}"></script>
    <script src="{!! asset('datepicker/bootstrap-datepicker.js') !!}"></script>
    <script>
        $(function(){
            $(function(){
                $('#reservationtime').on('click',function(){
                    if(!this.hasBindedPicker){
                        $(this).daterangepicker({
                            startDate: '{!! date('Y-m-d H:i:s',strtotime('-30 day')) !!}',
                            endDate: '{!! date('Y-m-d H:i:s') !!}',
                            timePicker24Hour: true,
                            timePickerSeconds: true,
                            timePicker: true,
                            locale:{
                                format: "YYYY-MM-DD HH:mm:ss",
                                applyLabel: "确定",
                                cancelLabel: "取消",
                            },
                            language:'cn'
                        });
                        this.hasBindedPicker = true;
                        $(this).click();
                    }
                })
            })
        })
    </script>
@endsection