
<div class="row">
    <div class="col-sm-6">
        <div class="form-group col-sm-12 forme-center">
            {!! Form::label('is_open_email_send_function', '是否开启邮箱发送功能').(Form::required_pin()) !!}
            <?php $statusDic = \App\Models\Conf\CarrierPasswordRecoverySiteConf::statusMeta() ?>
            @foreach($statusDic as $key => $value)
                @if(isset($carrierPasswordRecoverySiteConfs) && $carrierPasswordRecoverySiteConfs instanceof \App\Models\Conf\CarrierPasswordRecoverySiteConf && $carrierPasswordRecoverySiteConfs->is_open_email_send_function ==$key)
                    <label class="radio-inline">
                        <input type="radio"  value="{!! $key !!}" name="is_open_email_send_function" checked class="square-blue"><span class="icon-spacing">{!! $value !!}</span>
                    </label>
                @else
                    <label class="radio-inline">
                        <input type="radio"  value="{!! $key !!}"  name="is_open_email_send_function" class="square-blue"><span class="icon-spacing">{!! $value !!}</span>
                    </label>
                @endif
            @endforeach
        </div>
        <!-- Site Description Field -->
        <div class="form-group col-sm-8">
            {!! Form::label('smtp_server', 'smtp服务器') !!}
            {!! Form::text('smtp_server', null, ['class' => 'form-control']) !!}
        </div>
        <!-- Site Description Field -->
        <div class="form-group col-sm-8">
            {!! Form::label('smtp_service_port', 'smtp服务器端口') !!}
            {!! Form::text('smtp_service_port', null, ['class' => 'form-control']) !!}
        </div>
        <!-- Site Description Field -->
        <div class="form-group col-sm-8">
            {!! Form::label('mail_sender', '邮件发送人') !!}
            {!! Form::text('mail_sender', null, ['class' => 'form-control']) !!}
        </div>
        <!-- Site Description Field -->
        <div class="form-group col-sm-8">
            {!! Form::label('smtp_username', 'smtp账号') !!}
            {!! Form::text('smtp_username', null, ['class' => 'form-control']) !!}
        </div>
        <!-- Site Description Field -->
        <div class="form-group col-sm-8">
            {!! Form::label('smtp_password', 'smtp密码') !!}
            {!! Form::text('smtp_password',null, ['class' => 'form-control']) !!}
        </div>

    </div>
</div>




<div class="form-group col-sm-12">
    {!! Form::button('保存当前设置', ['class' => 'btn btn-primary','type' => 'submit']) !!}
</div>
<style type="text/css">
    .icon-spacing {
        font-size: 16px;
        text-align: center;
        display: inline-block;
        vertical-align: middle;
        width: 40px;
    }
</style>



<script>
    $(function () {
        //iCheck for checkbox and radio inputs
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
        //Flat red color scheme for iCheck
        $('input[type="checkbox"].square-blue, input[type="radio"].square-blue').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue'
        });
    })

</script>


