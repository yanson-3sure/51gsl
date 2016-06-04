@extends('layouts.master')
@section('title', '分析师个人主页')
@section('content')
    <div class="fixed">
        <div class="con">
            <div class="banner">
                <div class="gz">
                    <div class="teacher-a">
                        <div class="teacher-a-img"><img src="{{getAvatar($cur_user['avatar'])}}"></div>
                        <div class="teacher-a-xq">
                            <div class="teacher-a-xq-1">
                                <div class="teacher-a-xq-1-name">{{$cur_user['name']}}</div>
                                <a href="javascript:;">
                                    @if($isFollowing)
                                        <div class="teacher-a-xq-1-gz" data-type="1"  data-fuserid="{{$analyst['uid']}}" onclick="focus1(this,false)">
                                            已关注
                                        </div>
                                    @else
                                        <div class="teacher-a-xq-1-gz" data-type="0"  data-fuserid="{{$analyst['uid']}}" onclick="focus1(this,false)">
                                            + 关注
                                        </div>
                                    @endif
                                </a>
                            </div>
                            <div class="teacher-a-xq-2"><img src="/img/rz.png">
                                <span>认证：{{$analyst['role_name']}}</span>
                            </div>
                            <div class="teacher-a-xq-3">
                                <span>直播：{{ isset($cur_user['posts']) ? $cur_user['posts'] : 0}}</span>
                                <span>粉丝：{{ isset($cur_user['followers']) ? $cur_user['followers'] : 0}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="teacher-b">
                        <p>个人特色：</p>
                        <p>{{$analyst['feature']}}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="con">
            <div class="gz">
                <div class="qbzb">全部直播</div>
            </div>
        </div>
    </div>
    <div id="wrapper" class="wrap" style="overflow: hidden; left: 0px;">
        <div id="scroller" style="transition-property: transform; transform-origin: 0px 0px 0px; transform: translate(0px, -1334px) scale(1) translateZ(0px);">

            <div id="pullDown" class="" data-min="{{$minscore}}">
                <div class="sxcenter">
                    <span class="pullDownIcon"></span><span class="pullDownLabel">下拉刷新...</span>
                </div>
            </div>
            <ul id="thelist">
                {{--@include('shared.status_list')--}}
            </ul>

            <div id="pullUp" class="" data-max="{{$maxscore}}">
                <div class="sxcenter">
                    <span class="pullUpIcon"></span><span class="pullUpLabel">上拉加载更多...</span>
                </div>
            </div>

        </div>
    </div>

    {{--@include('shared.pinglun_div')--}}
    {{--@include('shared.userinfo')--}}
@endsection
@section('footer')
            <script src="/js/tanchu.js"></script>
            <script src="/js/iscroll.js"></script>
            <script src="/js/iscroll_load.js"></script>
            <link href="/css/jquery.fancybox.css" rel="stylesheet" type="text/css">
            <script src="/js/jquery.fancybox.pack.js"></script>
            <script>
                $(function(){
                    fancybox();
                });
            </script>
                <script>
                    {{--$(function () {--}}
                        {{--ScrollPagination($('.parents'), "/analyst/status-list?id={{$id}}&max={{$maxscore}}");--}}
                    {{--});--}}
                    {{--function pullDownAction () {--}}
                        {{--$.ajax({--}}
                            {{--url: "/analyst/status-list",--}}
                            {{--data:{"id":"{{$id}}","min":$('#pullDown').attr('data-min')},--}}
                            {{--type: "get",--}}
                            {{--dataType:'json',--}}
                            {{--success:function(data){--}}
                                {{--if(parseInt(data.min)>0) {--}}
                                    {{--$('#pullDown').attr('data-min',data.min);--}}
                                    {{--$('#thelist').prepend(data.content);--}}
                                {{--}--}}
                            {{--},--}}
                            {{--error:function(er){--}}
                                {{--myScroll.refresh();--}}
                                {{--scroll_lock = false;--}}
                            {{--}--}}
                        {{--});--}}
                        {{--myScroll.refresh();--}}
                    {{--}--}}

                    {{--function pullUpAction () {--}}
                        {{--$.ajax({--}}
                            {{--url: "/analyst/rev-status-list",--}}
                            {{--data:{"id":"{{$id}}","max":$('#pullUp').attr('data-max')},--}}
                            {{--type: "get",--}}
                            {{--dataType:'json',--}}
                            {{--success:function(data){--}}
                                {{--if(parseInt(data.max)>0) {--}}
                                    {{--scroll_lock = false;--}}
                                    {{--$('#pullUp').attr('data-max',data.max);--}}
                                    {{--$('#thelist').append(data.content);--}}
                                {{--}--}}
                            {{--},--}}
                            {{--error:function(er){--}}
                                {{--myScroll.refresh();--}}
                                {{--scroll_lock = false;--}}
                            {{--}--}}
                        {{--});--}}
                        {{--myScroll.refresh();--}}
                    {{--}--}}
                </script>
@endsection