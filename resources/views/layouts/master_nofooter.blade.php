<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title')-{{config('base.name')}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="/css/index.css" type="text/css" rel="stylesheet">
    <link href="/css/font-awesome.min.css?v={{config('base.version.css')}}" rel="stylesheet">
    @section('iscroll')
        <link href="/css/scrollbar.css" rel="stylesheet">
    @show
    @section('headcommon')

    @show

</head>
<body>
@yield('content')

<script src="/js/jquery.min.js"></script>
<script src="/js/jquery.form.js"></script>
<script src="/js/jquery.scrollPagination.js"></script>
<script src="/js/layer/layer.js"></script>
<script src="/js/common.js?v={{config('base.version.js')}}"></script>
@yield('footer')
</body>
</html>