@extends('layouts.master')
@section('title', '我的消息')
@section('content')
    <div id="wrapper" style="overflow: hidden; left: 0px; position:fixed; top:0;">
        <div id="scroller" style="transition-property: transform; transform-origin: 0px 0px 0px; transform: translate(0px, -1334px) scale(1) translateZ(0px);">
            <ul id="thelist">
                @include('my.message.common.list')
            </ul>
            <div id="pullUp" class=""  data-max="{{$max}}">
                <div class="sxcenter">
                    <span class="pullUpIcon"></span><span class="pullUpLabel">上拉加载更多...</span>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('footer')
    <script src="/js/iscroll.js"></script>
    <script src="/js/iscroll_load.js"></script>
    <link href="/css/jquery.fancybox.css" rel="stylesheet" type="text/css">
    <script src="/js/jquery.fancybox.pack.js"></script>
    <script>
        $(function(){
            fancybox();
        });
        function pullUpAction () {
            scroll_lock = true;
            $.ajax({
                url: "/my/message/rev-list",
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
