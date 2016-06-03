@extends('layouts.master_auth')
@section('title', '注册')
@section('body')
<body bgcolor="#eeebeb">
<div class="con">
    <div class="login_top">
        <div class="login_top_l"><a href="{{url('/auth/reg1')}}"><img src="/img/close.png"></a></div>
        用户注册
    </div>
    <form id="myFrom" method="post" action="/auth/reg2">
        <input type="hidden" value="{{$mobile}}" id="mobile" name="mobile">
    <div class="zc_inp">
        <div class="zc_nc"><input type="text" placeholder="请设置昵称" name="name" id="name" required /><div class="zc_ts" style="display:none; ">此昵称被占用</div></div>
        <div class="zc_nc"><input type="password" placeholder="请输入6位以上的密码" name="password" id="password" /><div class="zc_ts" style="display:none; ">密码不合格</div></div>
        <div class="zc_nc"><input type="password" placeholder="请再次输入密码" name="password_confirmation" id="password_confirmation" /><div class="zc_ts" style="display:none; ">密码不一致</div></div>
    </div>

    <div class="zc_btn"><div class="zc_btn_wc">完成</div></div>
    </form>
    <div class="zc_xx"><span>点击完成代表</span><a href="#" class="zc_xx_fwxx">同意《股思录服务协议》</a></div>
</div>
</body>
    <script>
        $(function(){
            $('.zc_btn_wc').click(function(){
                $("#myFrom").ajaxSubmit({
                    success: function(data) {
                        location.href = '/'
                    },
                });
            });

        });
    </script>
@stop