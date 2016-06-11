@extends('layouts.master')
@section('title', '直播详细')
@section('content')
    @include('status.common.item')
    @include('comment.common.create_div')
@endsection
@section('footer')
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
</script>
@endsection
