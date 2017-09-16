<!-- Pay Channel Id Field -->
<div class="form-group col-sm-12">
    {!! Form::label('def_pay_channel_id', '三方支付平台:') !!}
    <select name="def_pay_channel_id" class="form-control disable_search_select2" style="width: 100%;" onchange="selectOnchang(this)">
        <option value="0">请选择</option>
        @foreach($payChannelList as $key => $value)
            @if(isset($carrierThirdPartPay) &&  $carrierThirdPartPay->def_pay_channel_id == $value->id)
                <option value="{!! $value->id !!}" selected>{!! $value->channel_name !!}</option>
            @else
                <option value="{!! $value->id !!}">{!! $value->channel_name !!}</option>
            @endif
        @endforeach
    </select>
</div>

<!-- Merchant Number Field -->
<div class="form-group col-sm-12">
    {!! Form::label('merchant_number', '商户ID:') !!}
    {!! Form::text('merchant_number', null, ['class' => 'form-control']) !!}
</div>

<!-- Merchant Bind Domain Field -->
@if(isset($carrierThirdPartPay) && $carrierThirdPartPay->def_pay_channel_id != 20)
<div class="form-group col-sm-12 zhifu">
        {!! Form::label('private_key', '证书或密钥:') !!}
        {!! Form::textarea('private_key', '', ['class' => 'form-control','rows' => 3, 'style' => 'resize:none;']) !!}
    </div>
@else
    <div class="form-group col-sm-12 zhifu" style="display: none;">
        {!! Form::label('private_key', '证书或密钥:') !!}
        {!! Form::textarea('private_key', '', ['class' => 'form-control','rows' => 3, 'style' => 'resize:none;']) !!}
    </div>
@endif


@if(isset($carrierThirdPartPay) && $carrierThirdPartPay->def_pay_channel_id == 20)
    <div class="form-group col-sm-12 guofubao">
        {!! Form::label('merchant_identify_code', '商户识别码:') !!}
        {!! Form::textarea('merchant_identify_code', '', ['class' => 'form-control','rows' => 3, 'style' => 'resize:none;']) !!}
    </div>
@else
    <div class="form-group col-sm-12 guofubao" style="display: none;">
        {!! Form::label('merchant_identify_code', '商户识别码:') !!}
        {!! Form::textarea('merchant_identify_code', '', ['class' => 'form-control','rows' => 3, 'style' => 'resize:none;']) !!}
    </div>
@endif

@if(isset($carrierThirdPartPay) && $carrierThirdPartPay->def_pay_channel_id == 20)
    <div class="form-group col-sm-12 guofubao">
        {!! Form::label('vir_card_no_in', '国付宝转入账户:') !!}
        {!! Form::text('vir_card_no_in', null, ['class' => 'form-control']) !!}
    </div>
@else
    <div class="form-group col-sm-12 guofubao" style="display: none;">
        {!! Form::label('vir_card_no_in', '国付宝转入账户:') !!}
        {!! Form::text('vir_card_no_in', null, ['class' => 'form-control']) !!}
    </div>
@endif

<div class="form-group col-sm-12">
    {!! Form::label('merchant_bind_domain', '三方绑定商场域名:') !!}
    {!! Form::text('merchant_bind_domain', null, ['class' => 'form-control']) !!}
    <span style=" color: red;">
        例如:pay.shop.com，不需要带http:// 
        
    </span>
</div>

@if(isset($carrierThirdPartPay) && $carrierThirdPartPay->def_pay_channel_id != 20)
    <div class="form-group col-sm-12 zhifu">
        {!! Form::label('good_name', '商品名称:') !!}
        {!! Form::text('good_name', null, ['class' => 'form-control']) !!}
    </div>
@else
    <div class="form-group col-sm-12 zhifu" style="display: none;">
        {!! Form::label('good_name', '商品名称:') !!}
        {!! Form::text('good_name', null, ['class' => 'form-control']) !!}
    </div>
@endif

<div class="form-group col-sm-12" id="pay_ids_json">
    {!! Form::label('pay_json', '账户支付渠道:') !!}&nbsp;&nbsp;
    <input type="hidden" name="pay_ids_json" v-bind:value="payJson">
    <input type="checkbox" v-model="payJson" value="1" class="square-blue">
    银行卡&nbsp;&nbsp;&nbsp;&nbsp;
<!--    <input  type="checkbox" v-model="payJson" value="2" class="square-blue">
    支付宝&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="checkbox" v-model="payJson" value="3" class="square-blue">
    微信支付-->
</div>

<script>
    $(function(){
        new Vue({
            el:'#pay_ids_json',
            data:{
                payJson:[],
            },
            created:function(){
                @if(isset($carrierThirdPartPay))
                    this.payJson = $.parseJSON('{!! $carrierThirdPartPay->pay_ids_json !!}');
                @endif
            },
            computed:{
            }
        })
    })
</script>
<script>
    $(function(){
        $('.disable_search_select2').select2({
            minimumResultsForSearch: Infinity
        });
    })
</script>
<script type="text/JavaScript">
    function selectOnchang(obj) {
        var value = obj.options[obj.selectedIndex].value;
        var classes = {
            '.zhifu':19, //智付
            '.guofubao':20, //国付宝
        };
        $.each(classes,function (key,classesValue) {
            if (classesValue == value){
                $(key).show();
            }else{
                $(key).hide();
            }
        });
    }
</script>