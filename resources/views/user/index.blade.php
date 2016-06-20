@extends('layouts.master')
@section('title', '全部名师')
@section('content')
        <div class="con" >
            <!--顶部导航-->
            <nav class="navbar-default navbar-fixed-top">
                <div class="nav">
                    <ul>
                        <li><a href="{{url('/home?type=home')}}">关注</a></li>
                        <li><a href="{{url('/home')}}">全部</a></li>
                        <li style="width:34%;" class="tabin">名师</li>
                    </ul>
                </div>
            </nav>
            <!--名师-->
            <div class="cont">
                <div class="gz">
                    <div class="ms-nav">
                        <div class="ms-nav-1">
                            <a class="header" {{ $type!=2 && $type!=3 ? 'id=top' : ''  }} href="{{url('/user')}}">直播最多</a>
                            <a class="header" {{ $type==2 ? 'id=top' : '' }} href="{{url('/user?type=2')}}">人气最高</a>
                            <a class="header" {{ $type==3 ? 'id=top' : '' }} href="{{url('/user?type=3')}}">互动最多</a>
                        </div>
                    </div>
                    <div class="box">
                        @include('user.common.list')
                    </div>
                </div>
            </div>
        </div>
@endsection