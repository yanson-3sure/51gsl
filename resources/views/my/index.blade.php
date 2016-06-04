@extends('layouts.master')
@section('title', '我的')
@section('content')
    <div class="con">
        <!--toubu-->
        <div class="banner">
            @if($isLogin && $user['role']==1)
                <div class="fb"><a href="/my/status/create"> <img src="/img/fabiao.png"></a></div>
            @endif
            <div class="my">
                @if($isLogin)
                    @if($user['role']==1)
                        <a href="{{ url('/my/info')  }}">
                            <div class="myimg"><img src="{{  getAvatar($user['avatar'],80) }}"></div>
                        </a>
                        <p><a href="{{ url('/user/'.$user['id'])  }}">{{ $user['name'] }} </a>{{getTime()}}
                        </p>
                    @else
                        <a href="/my/info">
                            <div class="myimg"><img src="{{  getAvatar($user['avatar'],80) }}"></div>
                        </a>
                        <p><a href="/my/info">{{ $user['name'] }} </a>{{getTime()}}</p>
                    @endif
                @else
                    <a href="#"><div class="myimg"><img src="/img/wms.png"></div></a>
                    <p><a href="/auth/login">登录 </a><span>|</span><a href="/auth/reg1">注册</a></p>
                @endif
            </div>
        </div>

        <!--liebiao-->
        @if($isLogin && $user['role']==1)
            <a href="{{ url('/user/'.$user['id'])  }}">
                <div class="wdzy gz">
                    <div class="wdzy-l"><img src="/img/zy.png">

                        <p>我的主页</p></div>
                    <div class="wdzy-r"><img src="/img/dj.png"></div>
                </div>
            </a>
        @endif
        <a href="{{ url('/my/message')  }}">
            <div class="wdzy gz">
                <div class="wdzy-l"><img src="/img/xx.png">

                    <p>我的消息</p></div>
                <div class="person-a">
                    <span class="wdzy-l-1 person" style="display:none;"></span>
                    <span class="wdzy-l-2 person" style="display:none;">9</span>
                    <span class="wdzy-l-3 person" style="display:none;">99</span>
                    @if($noreadcount>0)
                        <span class="wdzy-l-4 person" style="display:block;">{{ $noreadcount  }}</span>
                    @endif
                </div>
                <div class="wdzy-r"><img src="/img/dj.png"></div>
            </div>
        </a>
        <a href="{{ url('/my/following')  }}">
            <div class="wdzy gz">
                <div class="wdzy-l"><img src="/img/gz.png">

                    <p>我的关注</p></div>
                <div class="wdzy-r"><img src="/img/dj.png"></div>
            </div>
        </a>

        @if($isLogin && $user['role']==0)
        <a href="/my/apply">
            <div class="wdzy gz">
                <div class="wdzy-l"><img src="/img/gz.png">

                    <p>投顾认证</p></div>
                <div class="wdzy-r"><img src="/img/dj.png"></div>
            </div>
        </a>
        @endif
        @if($isLogin)
            <a href="{{ url('/auth/logout')  }}">
                <div class="wdzy gz">
                    <div class="wdzy-l"><img src="/img/gz.png">
                        <p>退出</p></div>
                    <div class="wdzy-r"><img src="/img/dj.png"></div>
                </div>
            </a>
        @endif

        <div class="fotbot"></div>

@stop
