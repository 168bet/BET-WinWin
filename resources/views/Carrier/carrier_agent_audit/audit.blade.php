<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle">审核中</h4>
        </div>
        {!! Form::model($carrierAgentUser, ['route' => ['carrierAgentAudits.saveAudit', $carrierAgentUser->id], 'method' => 'patch']) !!}

        <div class="modal-body" id="modalContent">

            <div class="form-group col-sm-6">
                {!! Form::label('type', '代理类型:').Form::required_pin() !!}
                <?php $typeDic = \App\Models\CarrierAgentLevel::typeMeta() ?>
                <select id="type" name="" class="form-control carrier_bank_cards_select2" style="width: 100%;">
                    @foreach($typeDic as $key => $value)
                       @if(isset($type) && $type->type == $key)
                            <option value="{!! $key !!}" selected>{!! $value !!}</option>
                        @else
                            <option value="{!! $key !!}">{!! $value !!}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-6">
                {!! Form::label('agent_level_id', '代理名称').Form::required_pin() !!}
                <select name="agent_level_id" id="agent_level_id" class="selectpicker show-tick form-control">
                    @if(isset($carrierAgentLevelName))
                        @foreach($carrierAgentLevelName as $key => $value)
                           @if(isset($carrierAgentUser) && $carrierAgentUser instanceof \App\Models\CarrierAgentUser && $carrierAgentUser->agent_level_id == $value->id)
                                <option value="{!! $value->id !!}" selected>{!! $value->level_name !!}</option>
                            @else
                                <option value="{!! $value->id !!}">{!! $value->level_name !!}</option>
                            @endif
                        @endforeach
                    @endif    
                </select>  
            </div>
            <div class="form-group col-sm-6">
                {!! Form::label('audit_status', '审核状态').Form::required_pin() !!}
                    <label class="radio-inline"> 
                        <input type="radio"  value="1" name="audit_status" checked>通过  
                    </label> 
                    <label class="radio-inline"> 
                        <input type="radio"  value="2" name="audit_status">拒绝  
                    </label> 
            </div>
            <div class="form-group col-sm-12">
                {!! Form::label('customer_remark', '备注:') !!}
                {!! Form::textarea('customer_remark', null, ['class' => 'form-control','rows' => 5, 'style' => 'resize:none;']) !!}
            </div>
        </div>
        <div class="modal-footer">
            <div class="form-group col-sm-12">
                {!! TableScript::editFormSubmitAndCancelButtonsScript(Route('carrierAgentAudits.saveAudit',$carrierAgentUser->id)) !!}
            </div>
        </div>
        {!! Form::close() !!}

    </div>
</div>

<script>
$(function(){  
    $("#type").change(function(){  
        var objectModel = {};  
        var   value = $(this).val();  
        var   type = $(this).attr('id');  
        objectModel[type] =value;  
        $.ajax({  
            url:"{!! route('carrierAgentUsers.dataAgentLevel') !!}", //你的路由地址  
            type:"post",  
            dataType:"json",  
            data:objectModel,  
            timeout:30000,  
            success:function(data){  
                $("#agent_level_id").empty();  
                var count = data.length;  
                var i = 0;  
                var b="";
//                    b+="<option value=''>"+"请选择..."+"</option>"; 
                   for(i=0;i<count;i++){  
                       b+="<option value='"+data[i].id+"'>"+data[i].level_name+"</option>";  
                   }  
                $("#agent_level_id").append(b);  

            }  
        });  
    });  
})  
</script>