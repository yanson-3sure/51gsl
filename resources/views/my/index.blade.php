@extends('layouts.master')
@section('title', '我的')
@section('body')
        <!-- 头像区域 -->
<div class="head-container">
    @if($isLogin && $user['role']==1)
    <a class="write" href="/my/status/create"></a>
    @endif
    @if($isLogin)
        <div class="head-wrap">
            <div class="user_head" style="background-image:url({{ getAvatar($user['avatar'],60) }});">
                <a href="{{ url('/my/info')  }}" class="mask"></a>
            </div>
        </div>
        <p>{{ $user['name'] }},你好</p>
    @else
        <div class="head-wrap">
            <div class="user_head" style="background-image:url({{ getAvatar('',60) }});">
                <a href="{{ url('/my/info')  }}" class="mask"></a>
            </div>
        </div>
        <p><a href="/auth/login">登录</a> <a href="/auth/reg1">注册</a> </p>
    @endif
</div>

<!-- 功能列表区域 -->
<div class="bd">
    <div class="weui_cells weui_cells_access">
        @if($isLogin && $user['role']==1)
        <a href="{{ url('/user/'.$user['id'])  }}" class="weui_cell">
            <div class="weui_cell_hd">
                <img src="/img/zhuye.svg" alt="" width="16px" class="db mr6">
            </div>
            <div class="weui_cell_bd weui_cell_primary">我的主页</div>
            <div class="weui_cell_ft"></div>
        </a>
        @endif
        @if($isLogin && $user['role']==1)
            <?php $vipUrl = '/my/vip/provided'; ?>
        @else
            <?php  $vipUrl = '/my/vip';?>
        @endif
        <a  href="{{$vipUrl}}" class="weui_cell">
            <div class="weui_cell_hd">
                <img src="/img/vip.svg" alt="" width="16px" class="db mr6">
            </div>
            <div class="weui_cell_bd weui_cell_primary">我的服务</div>
            <div class="weui_cell_ft"></div>
        </a>
        <a href="{{ url('/my/message')  }}" class="weui_cell">
            <div class="weui_cell_hd">
                <img src="/img/wodexiaoxi.svg" alt="" width="16px" class="db mr6">
            </div>
            <div class="weui_cell_bd weui_cell_primary">我的消息
                @if($noreadcount)
                <div class="badge mt2">
                    @if($noreadcount>999)
                       999+
                    @else
                        {{$noreadcount}}
                    @endif
                </div>
                @endif
            </div>
            <div class="weui_cell_ft"></div>
        </a>
        <a href="{{ url('/my/follow')  }}" class="weui_cell">
            <div class="weui_cell_hd">
                <img src="/img/guanzhu.svg" alt="" width="16px" class="db mr6">
            </div>
            <div class="weui_cell_bd weui_cell_primary">我的关注</div>
            <div class="weui_cell_ft"></div>
        </a>
        @if($isLogin)
        <a href="/auth/logout" class="weui_cell">
            <div class="weui_cell_hd">
                <img src="/img/caogaoxiang.svg" alt="" width="16px" class="db mr6">
            </div>
            <div class="weui_cell_bd weui_cell_primary">退出</div>
            <div class="weui_cell_ft"></div>
        </a>
        @endif
    </div>
    {{--<div class="weui_cells weui_cells_access">--}}
        {{--<a href="kefu.html" class="weui_cell">--}}
            {{--<div class="weui_cell_hd">--}}
                {{--<img src="/img/kefu.svg" alt="" width="16px" class="db mr6">--}}
            {{--</div>--}}
            {{--<div class="weui_cell_bd weui_cell_primary">客服反馈</div>--}}
            {{--<div class="weui_cell_ft"></div>--}}
        {{--</a>--}}
    {{--</div>--}}
</div>
@endsection
