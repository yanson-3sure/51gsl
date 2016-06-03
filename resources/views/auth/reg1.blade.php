@extends('layouts.master_auth')
@section('title', '注册')
@section('body')
<body bgcolor="#eeebeb">
<div class="con">
    <div class="login_top">
        <div class="login_top_l"><a href="{{url('/my')}}"> <img src="/img/close.png"></a></div>
        用户注册
    </div>
<form method="post" id="myForm" action="/auth/reg1">
    <div class="zc_inp">
        <div class="zc_nc"><input type="text" placeholder="请输入手机号" maxlength="11"  name="mobile" id="mobile" value="" required  />
            <div class="zc_yzm fasong">获取验证码</div></div>

        <div class="zc_nc"><input type="text" placeholder="请输入验证码" name="captcha" id="captcha" required  /><div class="zc_yzm yanzm" style="display:none;">验证码错误</div></div>
    </div>
    <div class="zc_btn"><div class="zc_btn_wc">下一步</div></div>
</form>
    <div class="login_inp_hr"></div>
    <div class="login_inp_hz">第三方合作登陆</div>
    <div class="login_inp_wx"><div class="login_inp_wx_img"><a href="#"><img src="/img/wx.png"></a></div></div>
</div>

</body>
    <script>
        $(function(){
            sendSms('.fasong');
            $('.zc_btn_wc').click(function(){
                $('#myForm').ajaxSubmit({
                    success: function(data) {
                        location.href='/auth/reg2';
                    },
                });
            });
        });
    </script>
@endsection