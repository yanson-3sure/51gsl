<li>
    <div class="lecturer-wrapper">
        <div class="left">
            <div class="pub-header" style="background-image:url({{ getAvatar($user['avatar'],64)}})">
                <a href="/user/{{$user['id']}}"></a>
            </div>
        </div>
        <div class="right">
            <div class="guanzhu" status="{{ $user['following']?'1':'0' }}" data-fuserid="{{$user['id']}}"></div>
            <p class="pub-name">
                <a href="/user/{{$user['id']}}">{{$user['name']}}</a>
            </p>
            <p>
                粉丝：<span class="mr10">{{$user['followers']}}</span>
                直播：<span>{{$user['posts'] >0 ? $user['posts'] : 0}}</span>
            </p>
            <p class="brief-info">{{$user['feature']}}</p>
        </div>
    </div>
</li>