@extends('layouts.master')
@section('title', '直播')
@section('content')
    <nav class="navbar-default navbar-fixed-top">
        <div class="nav">
            <ul class="nav_ul">
                <li {{ empty($type) ? '' : 'class=tabin'  }} ><a href="{{url('/?type=my')}}">关注</a></li>
                <li {{ !empty($type) ? '' : 'class=tabin'  }}><a href="{{url('/')}}">全部</a></li>
                <li style="width:34%;"><a href="{{url('/analyst')}}">名师</a></li>
            </ul>
        </div>
    </nav>
    <div id="wrapper" style="overflow: hidden; left: 0px;">
        <div id="scroller" style="transition-property: transform; transform-origin: 0px 0px 0px; transform: translate(0px, -1334px) scale(1) translateZ(0px);">
            <div id="pullDown" class="" data-min="{{$min}}">
                <div class="sxcenter">
                    <span class="pullDownIcon"></span><span class="pullDownLabel">下拉刷新...</span>
                </div></div>
            <div class="xxk"><a href="/my/message?type=noread"><div class="xx" id="home_noreadcount" style="display: none;">33条消息</div></a></div>
            <ul id="thelist">
            @include('status.common.list')
            </ul>
            <div id="pullUp" class="" data-max="{{$max}}">
                <div class="sxcenter">
                    <span class="pullUpIcon"></span><span class="pullUpLabel">上拉加载更多...</span>
                </div>
            </div>
        </div>
    </div>
    <div id="ly" style="display:none"></div>
    @include('comment.common.create_div')<!--zhuanfa-->

    @include('user.common.userinfo')
@endsection
@section('footer')
        @parent
        <script src="/js/iscroll.js"></script>
        <script src="/js/iscroll_load.js"></script>
        <link href="/css/jquery.fancybox.css" rel="stylesheet" type="text/css">
        <script src="/js/jquery.fancybox.pack.js"></script>
        <script src="/js/jquery.praise.js"></script>
        <script src="/js/jquery.comment.js"></script>

        <script>
            $(function(){
                fancybox();
                $('.btn_praise').praise({uid:"{{$uid}}",avatar:"{{getAvatar($avatar)}}"});
                $('.fenxiang-pl').comment();
                $('.reply_comment').comment();
            });
            /**
             * 下拉刷新 （自定义实现此方法）
             */
            function pullDownAction () {
                    scroll_lock = true;
                    $.ajax({
                        url: "/home/status-list",
                        data:{"type":"{{$type}}","min":$('#pullDown').attr('data-min')},
                        type: "get",
                        dataType:'json',
                        success:function(data){
                            if(parseInt(data.min)>0) {
                                $('#pullDown').attr('data-min',data.min);
                                $('#thelist').prepend(data.content);
                                fancybox();
                            }
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
                        url: "/home/rev-status-list",
                        data:{"type":"{{$type}}","max":$('#pullUp').attr('data-max')},
                        type: "get",
                        dataType:'json',
                        success:function(data){
                            if(parseInt(data.max)>0) {
                                scroll_lock = false;
                                $('#pullUp').attr('data-max',data.max);
                                $('#thelist').append(data.content);
                                fancybox();
                            }
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


