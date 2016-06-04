<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')-{{config('base.name')}}</title>
    <script src="/js/jquery.min.js" ></script>
    <script src="/js/jquery.form.js"></script>
    <script src="/js/layer/layer.js"></script>
    <script src="/js/common.js"></script>
    <link href="/css/login.css" type="text/css" rel="stylesheet">
</head>
@yield('body')
</html>