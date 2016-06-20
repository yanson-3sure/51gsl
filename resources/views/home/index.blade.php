@extends('layouts.master')
@section('title', '直播')
@section('content')
    <nav class="navbar-default navbar-fixed-top">
        <div class="nav">
            <ul class="nav_ul">
                <li {{ $type=="all_home" ? '' : 'class=tabin'  }} ><a href="{{url('/home?type=home')}}">关注</a></li>
                <li {{ $type=='home' ? '' : 'class=tabin'  }}><a href="{{url('/')}}">全部</a></li>
                <li style="width:34%;"><a href="{{url('/user')}}">名师</a></li>
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
                    for (var i = 0; i < ids.length; i++) {
                        //console.log(ids[i]);
                        $('#gz_' + ids[i]).find('.btn_praise').praise({
                            uid: "{{$uid}}",
                            avatar: "{{getAvatar($avatar)}}"
                        });
                        $('#gz_' + ids[i]).find('.fenxiang-pl').comment();
                        $('#gz_' + ids[i]).find('.reply_comment').comment();
                    }
                    fancybox();
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


