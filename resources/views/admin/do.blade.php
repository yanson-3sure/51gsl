@extends('layouts.master')
@section('title', '直播')
@section('body-attr', 'class="home"')
@section('body')
<div id="status">开始....</div>

@endsection
@section('footer')
<script>
    $(function(){
        $('#status').click(function(){
            $(this).unbind("click");
            $.get('/admin/export/do?step=');
        });
    });
</script>
@endsection
