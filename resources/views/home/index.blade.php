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
    <div class="news-tip" style="display:none">
        <a href="/my/message?type=noread">
            <img src="/img/bell.svg" alt="">
            <span id="home_noreadcount">3条新消息</span>
        </a>
    </div>

    <!-- "关注"列表 -->
    <div id="wrapper">
        <div id="scroller"> <!-- class="content-wrapper" -->
            <div id="pullDown" data-min="{{$min}}">
                <span class="pullDownIcon"></span>
                <span class="pullDownLabel">下拉刷新</span>
            </div>
            <ul id="thelist">
                @include('status.common.list')
            </ul>
            <div id="pullUp" data-max="{{$max}}">
                <span class="pullUpIcon"></span>
                <span class="pullUpLabel">加载更多</span>
            </div>
        </div>
    </div>
    @include('comment.common.create_div')<!--zhuanfa-->
@endsection
@section('footer')
        @parent
        <script src="/js/iscroll.js"></script>
        <script src="/js/iscroll_load.js"></script>
        <script src="/js/jquery.praise.js"></script>
        <script src="/js/jquery.comment.js"></script>

        <script>
            $(function(){
                {{--$('.btn_praise').praise({uid:"{{$uid}}",avatar:"{{getAvatar($avatar)}}"});--}}
                {{--$('.fenxiang-pl').comment();--}}
                {{--$('.reply_comment').comment();--}}

            });
            var success = function(data){
                if(parseInt(data.max)>0) {
                    $('#pullUp').attr('data-max', data.max);
                    $('#thelist').append(data.content);
                }else if(parseInt(data.min)>0) {
                    $('#pullDown').attr('data-min', data.min);
                    $('#thelist').prepend(data.content);
                }
                if(data.content) {
                    scroll_lock = false;
                    var ids = data.ids;
                    {{--for (var i = 0; i < ids.length; i++) {--}}
                        {{--//console.log(ids[i]);--}}
                        {{--$('#gz_' + ids[i]).find('.btn_praise').praise({--}}
                            {{--uid: "{{$uid}}",--}}
                            {{--avatar: "{{getAvatar($avatar)}}"--}}
                        {{--});--}}
                        {{--$('#gz_' + ids[i]).find('.fenxiang-pl').comment();--}}
                        {{--$('#gz_' + ids[i]).find('.reply_comment').comment();--}}
                    {{--}--}}
                    {{--fancybox();--}}
                }
            }
            /**
             * 下拉刷新 （自定义实现此方法）
             */
            function pullDownAction () {
                    scroll_lock = true;
                    $.ajax({
                        url: "/status/list",
                        data:{"type":"{{$type}}","min":$('#pullDown').attr('data-min')},
                        type: "get",
                        dataType:'json',
                        success:function(data){
                            success(data);
                            myScroll.refresh();
                            scroll_lock = false;
                        },
                        error:function(er){
                            myScroll.refresh();
                            scroll_lock = false;
                        }
                    });
                myScroll.refresh();

            }
            /**
             * 滚动翻页 （自定义实现此方法）
             */
            function pullUpAction () {
                    scroll_lock = true;
                    $.ajax({
                        url: "/status/rev-list",
                        data:{"type":"{{$type}}","max":$('#pullUp').attr('data-max')},
                        type: "get",
                        dataType:'json',
                        success:function(data){
                            success(data);
                            scroll_lock = false;
                            myScroll.refresh();
                        },
                        error:function(er){
                            scroll_lock = false;
                            myScroll.refresh();
                        }
                    });
                    myScroll.refresh();
            }

        </script>
@endsection


