@extends('layouts.master')
@section('title', '策略列表')
@section('body-attr', 'class="pb80"')
@section('body')
        <!-- 策略列表 -->
<div id="wrapper" style='top:0'>
    <div id="scroller">
        <div id="pullDown">
            <span class="pullDownIcon"></span>
            <span class="pullDownLabel">下拉刷新</span>
        </div>
        <ul id="thelist">
            @foreach ($models as $model)
            <li>
                <!-- 试用中或已购买 -->
                <div class="lecture-wrap strategy-wrap">
                    <div class="head" style="background-image:url({{getAvatar($users[$model->uid]['avatar'])}})"></div>
                    <div class="main">
                        <a class="mask" href="/strategy/{{$model->id}}"></a>
                        <p class="title">
                            @if($model->vip)
                                <span>VIP</span>
                            @endif
                            <a href="/strategy/{{$model->id}}">{{$model->title}}</a>
                        </p>
                        <p class="sub-info">
                            <span>{{$users[$model->uid]['name']}}</span>
                            <span>{{ smarty_modifier_time_ago(strtotime($model['updated_at']) )}}</span>
                            <span>
                                @if($model['created_at']==$model['updated_at'])
                                    新增策略
                                @else
                                    更新策略
                                @endif
                            </span>
                        </p>
                    </div>
                    <div class="bottom">
                        <i></i>
                        <span>观看人数：{{$model->views}}</span>
                        <button><a href="/strategy/{{$model->id}}">立即阅读</a></button>
                        <button><a href="weipay.html">开通VIP</a></button>
                        <button class="freeuse">免费试用</button>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
        <div id="pullUp">
            <span class="pullUpIcon"></span>
            <span class="pullUpLabel">加载更多</span>
        </div>
    </div>
</div>
@endsection
@section('footer')
@endsection
