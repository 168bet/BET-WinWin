<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle">实际发放(最多:{!! $carrierAgentCommissionSettleLog->this_period_commission !!})</h4>
        </div>
        {!! Form::model($carrierAgentCommissionSettleLog, ['route' => ['carrierAgentSettleLogs.saveActualPayment',$carrierAgentCommissionSettleLog->id],'class' => 'form-horizontal','method' => 'PATCH']) !!}
        <div class="modal-body" id="playerAccountModalContent">
            <div class="col-sm-12">
                <div class="form-group">
                    <div class="form-group col-sm-12">
                        <label>金额</label>
                        <i class="fa fa-question-circle" style="color: #f44336"  data-toggle="tooltip" data-original-title="" ></i>
                        {!! Form::text('actual_payment', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
            <div class="form-group col-sm-12">
                {!! TableScript::editFormSubmitAndCancelButtonsScript(route('carrierAgentSettleLogs.saveActualPayment',$carrierAgentCommissionSettleLog->id)) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>