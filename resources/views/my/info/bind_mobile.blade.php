@extends('layouts.master_auth')
@section('title', '绑定手机号')
@section('body')
    <body bgcolor="#eeebeb">
    <div class="con">
        <div class="login_top">
            <div class="login_top_l"><a href="{{previous()}}" ><img src="/img/close.png"></a></div>
            用户绑定手机号
        </div>
        <form id="myForm" method="post" action="/my/info/bind-mobile">
        <div class="zc_inp">
            <div class="zc_nc">
                <input type="text" placeholder="请输入手机号" maxlength="11" onkeyup='this.value=this.value.replace(/\D/gi,"")' onafterpaste="value=value.replace(/\D/g,'')" name="mobile" id="mobile"   />
                <div class="zc_yzm fasong">获取验证码</div>
            </div>
            <div class="zc_nc">
                <input type="text" placeholder="请输入验证码" name="captcha" id="captcha" />
                <div class="zc_yzm yanzm" style="display:none;">验证码错误</div>
            </div>
        </div>
        </form>
        <div class="zc_btn"><div class="zc_btn_wc">绑定手机</div></div>
    </div>
    </body>
    <script>
        $(function(){
            sendSms('.fasong');
            $('.zc_btn_wc').click(function(){
                $('#myForm').ajaxSubmit({
                    success: function(data) {
                        layer.msg('绑定成功',function(){location.href = '/my/info';});
                    },
                });
            });

        });
    </script>
@stop