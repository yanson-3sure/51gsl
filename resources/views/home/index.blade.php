@extends('layouts.master')
@section('title', '直播')
@section('body-attr', 'class="home"')
@section('body')
        <!-- 顶部导航tab -->
    <div class="weui_navbar">
        <a href="{{url('/home?type=home')}}" class="weui_navbar_item {{ $type=="all_home" ? '' : 'weui_bar_item_on'  }}">
            关注
        </a>
        <a href="{{url('/')}}" class="weui_navbar_item {{ $type=='home' ? '' : 'weui_bar_item_on'  }}">全部</a>
        <a href="{{url('/user')}}" class="weui_navbar_item">名师</a>
    </div>

    <!-- 新的消息提示栏 -->
    <div class="news-tip" >
        <a href="/my/message?type=noread">
            <img src="/img/bell.svg" alt="">
            <span id="home_noreadcount">3条新消息</span>
        </a>
    </div>

    <!-- "关注"列表 -->
    <div id="wrapper">
        <div id="scroller"> <!-- class="content-wrapper" -->
            <div id="pullDown" data-url="/ajax/status?type={{$type}}">
                <span class="pullDownIcon"></span>
                <span class="pullDownLabel">下拉刷新</span>
            </div>
            <ul id="thelist"></ul>
            <div id="pullUp" data-url="/ajax/status?order=1&type={{$type}}">
                <span class="pullUpIcon"></span>
                <span class="pullUpLabel">加载更多</span>
            </div>
        </div>
    </div>
<!-- 删除成功提示 -->
<img src="/img/yishanchu.svg" id="deleteSuccess">

    @include('comment.common.create_div')<!--zhuanfa-->
@if(isAdmin($uid) || isAnalyst($role))
    @include('status.common.delete_div')
@endif
@endsection
@section('footer')
        @include('layouts.script_iscroll')
        @include('layouts.script_fancybox')
        <script src="/js/jquery.praise.js"></script>
        <script src="/js/jquery.comment.js"></script>
        @if(isAdmin($uid) || isAnalyst($role))
            <script src="/js/jquery.actionsheet.js"></script>
        @endif

        <script>
            var init =true;
            function callback(data){
                fancybox();
                $('.appreciate').praise();
                $('.make-comment').comment();
                $('.comment-content p').comment();
                @if(isAdmin($uid) || isAnalyst($role))
                $('.handle').actionsheet();
                @endif
                if(init){
                    $('#pullDown').attr('data-min',data.min);
                    init = false;
                }
            }
            $(function(){
                pullUpAction_exec(callback);
            });
        </script>
@endsection


