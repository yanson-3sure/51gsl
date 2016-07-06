@extends('layouts.master')
@section('title', '培训列表')
@section('body-attr', 'class="pb80"')
@section('body')
        <!-- 策略列表 -->
<div id="wrapper" style='top:0'>
    <div id="scroller">
        <ul id="thelist">
            @include('ajax.train.list')
        </ul>
    </div>
</div>
@endsection
@section('footer')
@endsection
