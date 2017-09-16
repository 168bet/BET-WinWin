<div class='btn-group'>
<!--    <a class='btn btn-default btn-xs' onclick="{!! TableScript::addOrEditModalShowEventScript(route('carrierActivities.edit', $id)) !!}">
        <i class="glyphicon glyphicon-edit">{!! Lang::get('common.edit') !!}</i>
    </a>-->
    <a class='btn btn-default btn-xs' href="{!! route('carrierActivities.edit',$id) !!}">
        <i class="glyphicon glyphicon-edit">{!! Lang::get('common.edit') !!}</i>
    </a>
</div>
{!! Form::close() !!}


