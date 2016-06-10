@extends('layouts.master')
@section('title', '直播详细')
@section('content')
    @include('status.common.item')
    @include('comment.common.create_div')
@endsection
@section('footers')
<link href="/css/jquery.fancybox.css" rel="stylesheet" type="text/css">
<script src="/js/jquery.fancybox.pack.js"></script>
<script>
    $(function(){
        fancybox();
    });
</script>
@endsection
