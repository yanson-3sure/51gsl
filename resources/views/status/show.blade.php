@extends('layouts.master')
@section('title', '直播详细')
@section('body')
    @include('status.common.item')
    @include('comment.common.create_div')
    @include('status.common.delete_div')
@endsection
@section('footer')
    @include('layouts.script_fancybox')
    <script src="/js/jquery.praise.js"></script>
    <script src="/js/jquery.comment.js"></script>

    <script>
        function callback(data){
            fancybox();
            $('.appreciate').praise();
            $('.make-comment').comment();
            $('.comment-content p').comment();
            deleteStatus('.handle');
        }
        $(function(){
            callback();
        });
    </script>
@endsection
