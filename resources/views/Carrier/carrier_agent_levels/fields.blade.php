<div class="form-group col-sm-12">
    {!! Form::label('level_name', '代理名称:').Form::required_pin() !!}
    {!! Form::text('level_name', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-12">
    {!! Form::label('type', '代理类型').Form::required_pin() !!}
    <?php $typeDic = \App\Models\CarrierAgentLevel::typeMeta() ?>
    <select id="type" name="type" class="form-control disable_search_select2" style="width: 100%;">
        @foreach($typeDic as $key => $value)
            @if(isset($carrierAgentLevel) && $carrierAgentLevel instanceof \App\Models\CarrierAgentLevel && $carrierAgentLevel->type == $key)
                <option value="{!! $key !!}" selected>{!! $value !!}</option>
            @else
                <option value="{!! $key !!}">{!! $value !!}</option>
            @endif
        @endforeach
    </select>
</div>

<div class="form-group col-sm-12">
    {!! Form::label('type', '是否支持多级代理').Form::required_pin() !!}
    <?php $multiAgentDic = \App\Models\CarrierAgentLevel::multiAgentMeta() ?>
    <select id="is_multi_agent" name="is_multi_agent" class="form-control disable_search_select2" style="width: 100%;">
        @foreach($multiAgentDic as $key => $value)
            @if(isset($carrierAgentLevel) && $carrierAgentLevel instanceof \App\Models\CarrierAgentLevel && $carrierAgentLevel->is_multi_agent == $key)
                <option value="{!! $key !!}" selected>{!! $value !!}</option>
            @else
                <option value="{!! $key !!}">{!! $value !!}</option>
            @endif
        @endforeach
    </select>
</div>

<!-- Default Player Level Field -->
<div class="form-group col-sm-12">
    {!! Form::label('default_player_level', '代理下属默认会员等级:') !!}
    <select id="default_player_level" name="default_player_level" class="form-control disable_search_select2" style="width: 100%;">
        @foreach($carrierPlayerLevels as $key => $value)
            @if(isset($carrierAgentLevel) && $carrierAgentLevel instanceof \App\Models\CarrierAgentLevel && $carrierAgentLevel->default_player_level == $value->id)
                <option value="{!! $value->id !!}" selected>{!! $value->level_name !!}</option>
            @else
                <option value="{!! $value->id !!}">{!! $value->level_name !!}</option>
            @endif
        @endforeach
    </select>
</div>


<div class="form-group col-sm-12">
    {!! Form::label('is_default', '是否是默认代理类型') !!}
    <?php $defaultAgentLevelMeta = \App\Models\CarrierAgentLevel::defaultAgentLevelMeta(); ?>
    <select name="is_default" class="form-control disable_search_select2" style="width: 100%;">
        @foreach($defaultAgentLevelMeta as $key => $value)
            @if(isset($carrierAgentLevel) && $carrierAgentLevel instanceof \App\Models\CarrierAgentLevel && $carrierAgentLevel->is_default == $key)
                <option value="{!! $key !!}" selected>{!! $value !!}</option>
            @else
                <option value="{!! $key !!}">{!! $value !!}</option>
            @endif
        @endforeach
    </select>
</div>

<div class="form-group col-sm-12">
    {!! Form::label('sort', '排序(数字越小越靠前)') !!}
    {!! Form::text('sort', 1, ['class' => 'form-control','required']) !!}
</div>

<div class="form-group col-sm-12">
    {!! Form::label('remark', '备注:') !!}
    {!! Form::textarea('remark', null, ['class' => 'form-control','rows' => 5, 'style' => 'resize:none;']) !!}
</div>
<script>
    $(function(){
        $('.disable_search_select2').select2({
            minimumResultsForSearch: Infinity
        });
    })
</script>


