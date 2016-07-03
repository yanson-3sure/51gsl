@extends('layouts.master')
@section('title', '我的关注')
@section('body')
        <!-- "名师"列表 -->
<div id="wrapper" style="top:0">
    <div id="scroller">
        <ul id="thelist">
            @include('my.follow.common.list')
        </ul>
    </div>
</div>
@endsection
@section('footer')
    <script src="/js/jquery.follow.js"></script>
    <script>
        $(function(){
            $('.guanzhu').follow();
        });
    </script>
@endsection