<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle">初审</h4>
        </div>
        {!! Form::model($carrierAgentCommissionSettleLog, ['route' => ['carrierAgentSettleLogs.saveTheTrial',$carrierAgentCommissionSettleLog->id],'class' => 'form-horizontal','method' => 'PATCH']) !!}
        <div class="modal-body" id="playerAccountModalContent">
            <div class="col-sm-12">
                <div class="form-group">
                    <div class="form-group col-sm-12">
                        <label>备注</label>
                        {!! Form::textarea('remark', null, ['class' => 'form-control','rows' => 5, 'style' => 'resize:none;']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
            <div class="form-group col-sm-12">
                {!! TableScript::editFormSubmitAndCancelButtonsScript(route('carrierAgentSettleLogs.saveTheTrial',$carrierAgentCommissionSettleLog->id)) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>