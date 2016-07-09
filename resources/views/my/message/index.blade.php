@extends('layouts.master')
@section('title', '我的消息')
@section('body-attr', 'class="msg"')
@section('body')
    <div id="wrapper" style="top:0">
        <div id="scroller">
            <ul id="thelist"></ul>
            <div id="pullUp" data-url="/my/message/rev-list?type={{$type}}">
                <span class="pullUpIcon"></span>
                <span class="pullUpLabel">加载更多</span>
            </div>
        </div>
    </div>
    <!-- 删除成功提示 -->
    <img src="/img/yishanchu.svg" id="deleteSuccess">
    @if(isAdmin($uid) || isAnalyst($role))
        @include('my.message.common.actionsheet')
    @endif
@endsection
@section('footer')
    @include('layouts.script_iscroll')
    @include('layouts.script_fancybox')
    @if(isAdmin($uid) || isAnalyst($role))
        <script src="/js/jquery.actionsheet.js"></script>
    @endif
    <script>
        function pullUpAction() {
            scroll_lock = true;
            pullUpAction_exec(callback);
            scroll_lock = false;
        }
        function callback(){
            fancybox();
            @if(isAdmin($uid) || isAnalyst($role))
                $('.body').actionsheet({op:'message'});
            @endif
    }
        pullUpAction();
        //iscrollInit();
        $(function(){

        });
    </script>
@endsection
