@extends('layouts.master_auth')
@section('title', '基本信息')
@section('body')
    <body bgcolor="#eeebeb">
    <div class="con">
        <div class="login_top">
            <div class="login_top_l"><a href="/my"> <img src="/img/fanhui.png"></a></div>
            个人信息
        </div>
        <ul class="xx_ul">
            <li id="fileupload"  data-url="/avatar-upload">
                @if(!isset($auditing))
                    <input type="file" name="file" id="doc"  accept="image/*">
                @endif
                <div class="xx_tx">
                    <div class="xx_tx_l">头像</div>
                    <div class="xx_tx_r">
                        <div class="xx_tx_img">
                            <a class="fancybox-effects" alt="头像" href="{{getAvatar($user['avatar'])}}">
                                <img src="{{getAvatar($user['avatar'],46)}}">
                            </a>
                        </div>
                        <div class="xx_tx_gd" style="margin-top:10px"><img src="/img/xx_gd.png"></div>
                    </div>
                </div>
            </li>
            <li>
                <div class="xx_nc">
                    <div class="xx_tx_l">昵称</div>
                    <div class="xx_tx_r">
                        <div class="xx_c"><input type="text" id="name" data-old="{{$user['name']}}" value="{{$user['name']}}" {{ isset($auditing) ? 'disabled' : '' }}/></div>
                        <div class="xx_tx_gd"><img src="/img/xx_gd.png"></div>
                    </div>
                </div>
            </li>
            <li><a href="/my/info/bind-mobile">
                    <div class="xx_nc">
                        <div class="xx_tx_l">手机号&nbsp;{{hideMobile($mobile)}}</div>
                        <div class="xx_tx_r">
                            <div class="xx_c">更换</div>
                            <div class="xx_tx_gd"><img src="/img/xx_gd.png"></div>
                        </div>
                    </div>
                </a></li>
            <li><a href="/my/info/modify-pwd">
                    <div class="xx_mm">
                        <div class="xx_tx_l">设置密码</div>
                        <div class="xx_tx_r">
                            <div class="xx_tx_gd"><img src="/img/xx_gd.png"></div>
                        </div>
                    </div>
                </a></li>
        </ul>
    </div>
    </body>
    @if(!isset($auditing))
        <script src="/js/jquery.ui.widget.js"></script>
        <script src="/js/jquery.fileupload.js"></script>
    @endif
    <link href="/css/jquery.fancybox.css" rel="stylesheet" type="text/css">
    <script src="/js/jquery.fancybox.pack.js"></script>
    <script>
        $(document).ready(function () {
            fancybox();
            @if(!isset($auditing))
            var buttonUpload = '#file';
            var settings = {
                autoUpload : true,
                add : function (e, data) {
                    data.submit();
                },
                submit : function (e, data) {
                    $(this).find(buttonUpload).attr('disabled', true);
                },
                done : function (e, data) {
                    if(data.result.error) {
                        layer.msg('ERROR:'+data.result.error);
                        return false;
                    }
                    d = new Date();
                    $('.fancybox-effects').attr('href',data.result.path+'@640w?'+d.getTime());
                    $('.fancybox-effects').find('img').attr('src',data.result.path+'@46w_46h?'+d.getTime());
                    layer.msg('上传成功');
                },
                stop : function (e) {
                    $(this).find(buttonUpload).removeAttr('disabled');
                    filesList = null;
                }
            }

            $('#fileupload').fileupload(settings);


            $("#name").click(function () {
                $("#name").select();
            })
            $('#name').blur(function(){
                var old = $(this).attr('data-old');
                if(old == $(this).val()){
                    return ;
                }
                var nickname = $(this).val();
                $.ajax({
                    url: "/my/info/chg-name",
                    data:{"name":nickname},
                    type: "post",
                    dataType:'json',
                    success:function(data){
                        if(data.result=="success"){
                            layer.msg('修改成功');
                            $(this).attr('data-old',nickname);
                        }
                    },
                });
            });
            @endif
        });
    </script>

@stop