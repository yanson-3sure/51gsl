@extends('layouts.master')
@section('title', $cur_user['name'].'个人主页')
@section('body')
 <!-- 全部直播 -->
<div id="wrapper" style="top:0">
    <div id="scroller">
        <div id="pullDown" data-url="/ajax/status?type=profile&uid={{$cur_user['id']}}">
            <span class="pullDownIcon"></span>
            <span class="pullDownLabel">下拉刷新</span>
        </div>
        <div >
            <!-- 讲师名片信息 -->
            @include('user.show_info')
            <!-- 导航tab -->
            <div class="weui_navbar" style="position:static">
                <a href="javascript:;" class="weui_navbar_item weui_bar_item_on_other">全部直播</a>
                @if($trains)
                <a href="/user/{{$cur_user['id']}}?type=train" class="weui_navbar_item">培训</a>
                @endif
                <a href="/user/{{$cur_user['id']}}?type=strategy" class="weui_navbar_item">策略</a>
            </div>
            <ul id="thelist"></ul>
            </div>
            <div id="pullUp" data-url="/ajax/status?order=1&type=profile&uid={{$cur_user['id']}}">
                <span class="pullUpIcon"></span>
                <span class="pullUpLabel">加载更多</span>
            </div>
        </div>

    <!-- 删除成功提示 -->
    <img src="img/yishanchu.svg" id="deleteSuccess">

    @include('comment.common.create_div')<!--zhuanfa-->
    @include('status.common.delete_div')
@endsection
@section('footer')
            @include('layouts.script_iscroll')
            @include('layouts.script_fancybox')
        <script src="/js/jquery.praise.js"></script>
        <script src="/js/jquery.comment.js"></script>
        <script src="/js/jquery.follow.js"></script>
        <script>
            $(function(){
                $('.guanzhu').follow({
                    no_css:{"color":"#ffffff","background-color":"transparent"},
                    ok_css:{"color":"#ffffff","background-color":"#ff4444"}
                });
            });
        </script>
        <script>
            var init =true;
            function callback(data){
                fancybox();
                $('.appreciate').praise();
                $('.make-comment').comment();
                $('.comment-content p').comment();
                deleteStatus('.handle');
                if(init){
                    $('#pullDown').attr('data-min',data.min);
                    init = false;
                }
            }
            $(function(){
                pullUpAction();
            });
        </script>
@endsection