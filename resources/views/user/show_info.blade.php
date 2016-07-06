<div class="lecturer-wrap namecard">
    <div class="left">
        <div class="pub-header" style="background-image:url({{getAvatar($cur_user['avatar'])}})">
            <a href="/user/{{$cur_user['id']}}"></a>
        </div>
    </div>
    <div class="right">
        <div class="guanzhu" status="{{$isFollowing ? '1' : '0'}}" name="other"  data-fuserid="{{$cur_user['id']}}">关注</div>
        <p class="pub-name">
            <a href="/user/{{$cur_user['id']}}">{{$cur_user['name']}}</a>
        </p>
        <p class="qualification">
            <i></i>
            <span>{{$analyst['role_name']}}</span>
        </p>
        <p class="tongji">
            粉丝：<span class="mr10">{{ isset($cur_user['followers']) ? $cur_user['followers'] : 0}}</span>
            直播：<span>{{ isset($cur_user['posts']) ? $cur_user['posts'] : 0}}</span>
        </p>
        <p class="brief-info">{{$analyst['feature']}}</p>
    </div>
</div>