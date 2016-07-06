@extends('layouts.master')
@section('title', '策略列表')
@section('body-attr', 'class="pb80"')
@section('body')
        <!-- 策略列表 -->
<div id="wrapper" style='top:0'>
    <div id="scroller">
        <ul id="thelist"></ul>
        <div id="pullUp" data-url="/ajax/strategy">
            <span class="pullUpIcon"></span>
            <span class="pullUpLabel">加载更多</span>
        </div>
    </div>
</div>
@endsection
@section('footer')
    @include('layouts.script_iscroll')
    <script>
        function pullUpAction () {
            scroll_lock = true;
            pullUpAction_exec();
            scroll_lock = false;
        }
        $(function(){
            pullUpAction_exec();
        });
    </script>
@endsection
