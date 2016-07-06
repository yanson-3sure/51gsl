<!doctype html>
<html @yield('html-attr')>
<head>
    <meta charset="utf-8">
    <title>@yield('title')-{{config('base.name')}}</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" type="text/css" href="/css/weui.min.css?v={{config('base.version.css')}}">
    <link rel="stylesheet" type="text/css" href="/css/main.css?v={{config('base.version.css')}}">
    <link rel="stylesheet" type="text/css" href="/css/phocus.css?v={{config('base.version.css')}}">
    <script src="/js/jquery.js"></script>
    @yield('head')
</head>
<body @yield('body-attr')>
@yield('body')
@section('footer_nav')
@include('layouts.footer_nav')
@if($isLogin)
    <script>$(function(){getNoreadcount()});</script>
@endif
@show
<script src="/js/jquery.form.js"></script>
<script src="/js/layer/layer.js"></script>
<script src="/js/main.js?v={{config('base.version.js')}}"></script>
@yield('footer')
</body>
</html>