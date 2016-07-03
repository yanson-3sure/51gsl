<div class="lecturer-wrapper">
    <div class="left">
        <div class="pub-header" style="background-image:url({{getAvatar($f_user['avatar'],64)}})">
            <a href="/user/{{$f_user['id']}}"></a>
        </div>
    </div>
    <div class="right">
        @if(isset($f_user['following']) && !$f_user['following'])
            <div class="guanzhu" status="0" data-fuserid="{{$f_user['id']}}">未关注</div>
        @else
            <div class="guanzhu" status="1" data-fuserid="{{$f_user['id']}}">已关注</div>
        @endif
        <p class="pub-name">
            <a href="{{$f_user['name']}}">福牛哥</a>
        </p>
        <p>
            粉丝：<span>{{$f_user['followers']}}</span>&nbsp;&nbsp;
            直播：<span>{{$f_user['posts']}}</span>
        </p>
        <p class="brief-info">{{$f_user['feature']}}</p>
    </div>
</div>