{{--邮箱--}}
@if(($conf->player_email_conf_status & 2) == 2)
    <div class="register_item">
        <label><b>*</b>电子邮件</label>
        <input type="text" name="email" id="email" placeholder="请输入电子邮件地址" required autocomplete="off" data-rule-email='true' data-msg-required='请输入有效的邮箱地址' data-msg-date='邮箱格式不正确'/>
        <span class="tips">请务必输入真实有效的电子邮件</span>
        <span class="valid"></span> 
    </div>
    
@elseif(($conf->player_email_conf_status &1) == 1)
    <div class="register_item">
        <label>电子邮件</label>
        <input type="text" name="email" id="email" placeholder="请输入电子邮件地址" autocomplete="off" data-rule-email='true' data-msg-required='请输入有效的邮箱地址' data-msg-date='邮箱格式不正确'/>
        <span class="tips">请务必输入真实有效的电子邮件</span>
        <span class="valid"></span>
    </div>
    
@else
@endif



