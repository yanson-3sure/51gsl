@extends('layouts.master_auth')
@section('title', '申请进度')
@section('body')
    <body bgcolor="#eeebeb">
    <div class="con">
        <div class="login_top">
            <div class="login_top_l"><a href="{{previous()}}"> <img src="/img/fanhui.png"></a></div>
            申请投顾认证
        </div>
            <ul class="xx_ul">
                <li>
                    <div class="xx_tx">
                        <div class="xx_tx_l">头像</div>
                        <div class="xx_tx_r">
                            <div class="xx_tx_img">
                                <a class="fancybox-effects" title="" href="{{getAvatar($user['avatar'],0)}}">
                                    <img src="{{getAvatar($user['avatar'])}}"></a>
                            </div>
                            <div class="xx_tx_gd" style="margin-top:10px"></div>
                        </div>
                    </div>
                </li>
                <form id="form" method="post" action="/my/apply" >
                    <li>
                        <div class="xx_nc">
                            <div class="xx_tx_l">昵称</div>
                            <div class="tg_inp">
                                <input type="text" placeholder="昵称认证成功后将不可更改" name="name" value="{{$user['name']}}" disabled />
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="xx_nc">
                            <div class="xx_tx_l">身份证号码</div>
                            <div class="tg_inp"><input type="text" placeholder="请输入证件号" value="{{$applyication->id_number}}" name="id_number" disabled /></div>
                        </div>
                    </li>
                    <li>
                        <div class="tg_li">
                            <div class="xx_tx_l">证券从业资格编号</div>
                            <div class="tg_inp"><input type="text" placeholder="请输入资格编号" name="securities_certificate" value="{{$applyication->securities_certificate}}" disabled/></div>
                        </div>
                    </li>
                    <li>
                        <div class="xx_nc">
                            <div class="xx_tx_l">认证内容</div>
                            <div class="tg_inp"><input type="text" maxlength="10" name="role_name" placeholder="5到10个字以内" value="{{$applyication->role_name}}" disabled /></div>
                        </div>
                    </li>
                    <li>
                        <div class="tg_sm">认证内容建议为机构+身份的形式 如：华讯财经分析师</div>
                    </li>
                    <li>
                        <div class="tg_jj">
                            <div class="xx_tx_l">个人简介</div>
                            <div class="tg_inp">
                        <textarea type="text" name="feature" maxlength="26" placeholder="4到26个字"
                                  class="tg_text" disabled>{{$applyication->feature}}</textarea></div>
                        </div>
                    </li>
                    <li>
                        <div class="tg_sm">个人简介和认证内容将在你的个人页面进行展示</div>
                    </li>
                </form>
            </ul>

            <div class="tg_fot">
                @if($applyication->audit==0)
                    <p>您的身份信息正在审核，如果有其他问题.</p>
                @elseif($applyication->audit==1)
                    <p>您的审核已通过，如需更改，</p>
                @elseif($applyication->audit==2)
                    <p>您的审核未通过,原因如下:</p>
                    <p>{{$applyication->reason}}</p>
                @endif
                    <p>请拨打电话：<span>4008 987 966</span></p>
            </div>
    </div>
    </body>
    <link href="/css/jquery.fancybox.css" rel="stylesheet" type="text/css">
    <script src="/js/jquery.fancybox.pack.js"></script>
    <script>
        $(document).ready(function () {
            fancybox();
        });
    </script>
@stop