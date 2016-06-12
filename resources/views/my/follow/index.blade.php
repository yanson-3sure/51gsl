@extends('layouts.master')
@section('title', '我的关注')
@section('content')
    <div class="con">
        @foreach($following_users as $user)
            <div class="yh">
                <div class="gz">
                    <div class="ms">
                        <div class="ms-l">
                            <div class="ms-l-img"><a href="{{ url('/user/'.$user['id'])  }}"><img src="{{getAvatar($user['avatar'],64)}}"></a></div>
                        </div>
                        <div class="ms-r">
                            <div class="ms-r-1">
                                <a href="{{ url('/user/'.$user['id'])  }}">{{$user['name']}}</a>
                                @if($user['following'])
                                    <div class="ms-r-1-r" data-type="1" data-fuserid="{{$user['id']}}" onclick="focus1(this)">已关注</div>
                                @else
                                    <div class="ms-r-1-r2" data-type="0" data-fuserid="{{$user['id']}}" onclick="focus1(this)">关注</div>
                                @endif
                            </div>
                            <p class="ms-r-2">
                                <span class="fans">粉丝数：</span><span class="gold">{{$user['followers']}}</span>
                                <span class="fans ms-r-2-4">直播：</span><span class="red">{{$user['posts']}}</span>
                            </p>
                            <p><span class="ms-r-3">个人特色：</span>
                                <span class="ms-r-3-1">{{$user['feature']}}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hr-2"></div>
        @endforeach
    </div>
@endsection