@extends('layouts.master')
@section('title', '培训列表')
@section('body-attr', 'class="pb80"')
@section('body')
        <!-- 策略列表 -->
<div id="wrapper" style='top:0'>
    <div id="scroller">
        <ul id="thelist">
            @foreach ($models as $model)
                <li>
                    <!-- 课程列表:已购买或试用期 -->
                    <div class="lecture-wrap">
                        <div class="left">
                            <div class="cover-img" style="background-image:url({{getTrainImageUrl($model->image,100)}})">
                                <a class="cover" href="/train/{{$model->id}}"></a>
                            </div>
                        </div>
                        <div class="main">
                            <a class="mask" href="/train/{{$model->id}}"></a>
                            <p class="title">
                                @if($model->vip)
                                    <span>VIP</span>
                                @endif
                                {{$model->title}}
                            </p>
                            <p>讲师：{{$users[$model->uid]['name']}}</p>
                            <p>时间：{{$model->time}}</p>
                            <p>价格：<span class="price">{{$model->price}}元/月</span></p>
                        </div>
                        <div class="bottom">
                            <i></i>
                            <span>观看人数：{{$model->views}}</span>
                            <button><a href="/train/{{$model->id}}">立即观看</a></button>
                            <button><a href="weipay.html">开通VIP</a></button>
                            <button class="freeuse">免费试用</button>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
@section('footer')
@endsection
