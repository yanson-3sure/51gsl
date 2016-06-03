@extends('layouts.master_auth')
@section('title', '找回密码')
@section('body')
    <body bgcolor="#eeebeb">
    <div class="con">
        <div class="login_top">
            <div class="login_top_l"><a href="{{previous()}}"><img src="/img/close.png"></a> </div>
            找回密码
        </div>
        <form id="myForm" method="post" action="/auth/find-pwd">
            <ul class="sz_ul">
                <li>
                    <div class="sz_inp"><input type="text" placeholder="请输入手机号" id="mobile" name="mobile" /></div>
                </li>
                <li>
                    <div class="sz_inp">
                        <div class="sz_inp_l"><input type="text" placeholder="验证码" name="captcha" id="captcha"/></div>
                        <div class="sz_inp_r">获取验证码</div>
                    </div>
                </li>
                <li>
                    <div class="sz_inp"><input type="password" placeholder="请输入6位以上密码"  name="password" id="password"  /></div>
                </li>
                <li>
                    <div class="sz_inp"><input type="password" placeholder="再次确认密码" name="password_confirmation" id="password_confirmation"/></div>
                </li>
                <li>
                    <div class="sz_tj">
                        <div class="sz_tj_btn">提交</div>
                    </div>
                </li>
                <li><a href="/auth/login">
                        <div class="yyzh">已有账号登录</div>
                    </a></li>
            </ul>
        </form>
    </div>
    </body>
    <script>
        $(function(){
            sendSms('.sz_inp_r');
            $('.sz_tj_btn').click(function(){
                $("#myForm").ajaxSubmit({
                    success: function(data) {
                        layer.msg('成功,自动登录中...',function(){location.href = data.intended;});
                    },
                });
            });
        });
    </script>
@stop