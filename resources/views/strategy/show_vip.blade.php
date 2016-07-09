@extends('layouts.master')
@section('title', '策略详细')
@section('body-attr', 'class="bfff"')
@section('body')
        <!-- 策略头部 -->
<div class="stragegy-head">
    <div class="pub-header" style="background-image:url({{getAvatar($model->user['avatar'])}})">
        <a href="/user/{{$model->uid}}"></a>
    </div>
    <h3>@if($model->vip)<img src="/img/vip.svg" width="22px">@endif{{$model->title}}</h3>
    <div>
        <span class="pub-name"><a href="/user/{{$model->uid}}">{{$model->user['name']}}</a></span>
        <span class="999">{{ smarty_modifier_time_ago(strtotime($model['updated_at']) )}}</span>
        <span class="view">阅读：{{$model->views}}</span>
    </div>
    <!-- 对于讲师，显示i隐藏a；对于普通用户，显示a隐藏i; -->
    @if(isAnalyst($user['role']) && $uid == $model->uid)
        <i class="handle"></i>
    @else
    <a href="/user/{{$model->uid}}" class="home">
        <img src="/img/zhuye.svg" alt="" width="22px">
    </a>
    @endif
</div>

<!-- 文章摘要 -->
<div class="disc">
    <h3>摘要</h3>
    <p>{{$model->intro}}</p>
</div>

<div class="huanyingkaitong">
    欢迎开通<span>{{$model->user['name']}}</span>vip会员，VIP会员可免费观看培训视频，免费阅读福牛歌投资策略，实时跟踪策略内容。
</div>

<!-- 大按钮 -->
<div class="btn-lg-wrap">
    <button class="btn-lg free" data-product-id="{{$model->uid}}">免费试用</button>
    <a href="/my/order/create?product_id={{$model->uid}}"><button class="btn-lg">开通VIP</button></a>
    <p class="red">风险提示：{{$model->risk}}</p>
</div>

<!-- 开通免费试用提示框 -->
<div class="weui_dialog_confirm" id="dialog2" style="display:none">
    <div class="weui_mask"></div>
    <div class="weui_dialog">
        <div class="weui_dialog_hd">
        </div>
        <div class="weui_dialog_bd textcenter">
            您获得了<span>{{$model->user['name']}}</span>一个月的VIP会员权限！
            <p>点击"我的——VIP服务"可查看</p>
        </div>
        <div class="weui_dialog_ft">
            <a href="/my/vip" class="weui_btn_dialog check">查看</a>
            <a href="javascript:;" class="weui_btn_dialog close">关闭</a>
        </div>
    </div>
</div>
@endsection
@section('footer_nav')
@endsection
@section('footer')
    <script src="/js/jquery.order.js"></script>
    <script>
        $(function(){
            $('button.free').order();
        });
    </script>
@endsection
