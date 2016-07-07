@extends('layouts.master')
@section('title', '培训详细')
@section('body-attr', 'class="fff"')
@section('body')
        <!-- 视频封面图 -->
<div class="videowrapper" style="background-image:url({{getTrainImageUrl($model->image)}})">
    <a href="/train/{{$model->id}}"><img src="/img/zhuye.svg" alt="" width="22px"></a>
</div>

<!-- 视频信息 -->
<div class="info-wrap bdb">
    <h3 class="bdb lh36 f16">课程详情</h3>
    <p class="lh36 f16">{{$model->title}}</p>
    <p class="999">课程内容:{{$model->content}}</p>
    <p class="lh28">
        价格：
        <span class="yellow">{{$model->price}}元/月</span>
    </p>
    <p class="lh28">时间：{{$model->time}}</p>
</div>


<!-- 大按钮 -->
<div class="btn-lg-wrap">
    <p class="yellow">新用户优惠：</p>
    <p>点击免费试用即可获得一个月的VIP服务</p>
    <button class="btn-lg free" data-product-id="{{$model->uid}}">免费试用</button>
    <button class="btn-lg">
        <a href="/my/order/create?product_id={{$model->uid}}">开通VIP</a>
    </button>
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
@section('footer')
    <script src="/js/jquery.order.js"></script>
    <script>
        $(function(){
            // 设置视频高度
            var wid = window.screen.width;
            var h = wid*9/16;
            $(".videowrapper").css("height",h + "px");
            $('button.free').order();
        });
    </script>
@endsection
