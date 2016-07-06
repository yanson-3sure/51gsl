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

                <a href="javascript:;" class="weui_navbar_item weui_bar_item_on_other">培训</a>

                <a href="/user/{{$cur_user['id']}}?type=strategy" class="weui_navbar_item">策略</a>
            </div>
            <ul id="thelist">
                @include('ajax.train.list')
            </ul>
        </div>
    </div>
    @endsection
    @section('footer')
        <script src="/js/jquery.follow.js"></script>
        <script>
            $(function(){
                $('.guanzhu').follow({
                    no_css:{"color":"#ffffff","background-color":"transparent"},
                    ok_css:{"color":"#ffffff","background-color":"#ff4444"}
                });
            });
        </script>
@endsection