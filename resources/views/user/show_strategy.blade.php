@extends('layouts.master')
@section('title', $cur_user['name'].'个人主页')
@section('body')
        <!-- 全部直播 -->
<div id="wrapper" style="top:0">
    <div id="scroller">
        <div >
            <!-- 讲师名片信息 -->
            @include('user.show_info')
                    <!-- 导航tab -->
            <div class="weui_navbar" style="position:static">
                <a href="/user/{{$cur_user['id']}}" class="weui_navbar_item">全部直播</a>

                @if($trains)
                    <a href="/user/{{$cur_user['id']}}?type=train" class="weui_navbar_item">培训</a>
                @endif

                <a href="javascript:;" class="weui_navbar_item weui_bar_item_on_other">策略</a>
            </div>
            <ul id="thelist"></ul>
            <div id="pullUp" data-url="/ajax/strategy?uid={{$cur_user['id']}}">
                <span class="pullUpIcon"></span>
                <span class="pullUpLabel">加载更多</span>
            </div>
        </div>
    </div>
    @endsection
    @section('footer')
        <script src="/js/jquery.follow.js"></script>
        @include('layouts.script_iscroll')
        <script>
            $(function(){
                $('.guanzhu').follow({
                    no_css:{"color":"#ffffff","background-color":"transparent"},
                    ok_css:{"color":"#ffffff","background-color":"#ff4444"}
                });
                pullUpAction_exec();
            })
        </script>
@endsection