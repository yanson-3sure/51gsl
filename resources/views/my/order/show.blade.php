@extends('layouts.master')
@section('title', '微信支付')
@section('body-attr', 'class="bfff"')
@section('body')
    <div class="weui_msg">
        <div class="weui_icon_area">
            <i class="weui_icon_success weui_icon_msg"></i>
        </div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">支付成功</h2>
            <p class="lh36">产品信息：{{$model->user['name']}}的VIP服务</p>
            <p class="yellow">产品期限：{{date('Y-m-d',strtotime($model->start_at))}}至{{date('Y-m-d',strtotime($model->end_at))}}</p>
        </div>
        <div class="weui_btn_area">
            <a href="/my/vip" class="weui_btn weui_btn_primary">查看</a>
            <a href="/home" class="weui_btn weui_btn_default">返回</a>
        </div>
    </div>

@endsection
@section('footer_nav')
@endsection
@section('footer')
@endsection