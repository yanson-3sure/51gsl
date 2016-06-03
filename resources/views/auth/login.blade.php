@extends('layouts.master_auth')
@section('title', '登录')
@section('body')
<body bgcolor="#eeebeb">
<div class="con">

    <div class="login_top">
        <div class="login_top_l"><a href="{{previous()}}"><img src="/img/close.png"></a></div>
        用户登录
    </div>
    <form id="form" method="post" action="/auth/login">

    <div class="login_inp">
        <ul class="login_inp_ul">
            <li class="login_inp_a"><input type="text" placeholder="请输入手机号" style="border:none;" name="mobile" id="mobile" value=""></li>
            <li class="login_inp_a"><input type="password" placeholder="请输入密码" style="border:none;" name="password" id="password" value=""></li>
            <li><div class="login_inp_check"><input type="checkbox" checked name="remember" style="width:13px; height:13px;">自动登录</div><a href="/auth/find-pwd" class="login_inp_wjmm">忘记密码</a></li>
            <li><div class="login_inp_btn"><img src="/img/dl.png"></div></li>
            <li><div class="login_inp_hr"></div></li>
            <li><div class="login_inp_hz">第三方合作登陆</div></li>
            <li><div class="login_inp_wx"><div class="login_inp_wx_img"><a href="#"><img src="/img/wx.png"></a></div></div></li>
            <li><div class="login_inp_zc"><span>还没有账号？</span><a href="/auth/reg1" class="zc">立即注册</a></div></li>
        </ul>
    </div>
    </form>
</div>
</body>
    <script>
        $(function(){
            $('.login_inp_btn').click(function(){
                jQuery(form).ajaxSubmit({
                    success: function(data) {
                        if(data.auth){
                            location.href= data.intended;
                        }else{
                            layer.msg('手机号或密码不正确');
                        }
                    },
                });
            });

        });
    </script>
@stop