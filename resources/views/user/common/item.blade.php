<div class="yh">
    <div class="ms-l">
        <div class="ms-l-img"><a href="{{ url('/user/'.$user['id'])  }}">
                <img src="{{ getAvatar($user['avatar'],64)}}"></a></div>
    </div>
    <div class="ms-r">
        <div class="ms-r-1">
            <a href="{{ url('/user/'.$user['id'])  }}" target="_self" class="ms-r-1-l">{{$user['name']}}</a>
            @if($user['following'])
                <div class="ms-r-1-r" data-type="1" data-fuserid="{{$user['id']}}" onclick="focus1(this)">已关注</div>
            @else
                <div class="ms-r-1-r2" data-type="0" data-fuserid="{{$user['id']}}" onclick="focus1(this)">关注</div>
            @endif
        </div>
        <p class="ms-r-2">
            <span class="fans">粉丝数：</span><span class="gold">{{$user['followers']}}</span>
            <span class="fans ms-r-2-4">直播：</span><span class="red">{{$user['posts'] >0 ? $user['posts'] : 0}}</span>
        </p>
        <p class="tese"><span class="ms-r-3">个人特色：</span>
            <span class="ms-r-3-1">{{$user['feature']}}</span>
        </p>
    </div>
</div>