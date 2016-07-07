<div class="lecture-wrap strategy-wrap">
@if(!$profile)
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
@else
    <div class="main">
        <a class="mask" href="/strategy/{{$model->id}}"></a>
        <p class="title">
            @if($model->vip)
                <span>VIP</span>
            @endif
            <a href="/strategy/{{$model->id}}">{{$model->title}}</a>
        </p>
        <p class="sub-info">
            <span>{{ smarty_modifier_time_ago(strtotime($model['updated_at']) )}}</span>
            <span>
                @if($model['created_at']==$model['updated_at'])
                    新增策略
                @else
                    更新策略
                @endif
            </span>
        </p>
        <p class="mb6">简介信息简介信息简介信息简介信息简介信息简介信息简介信息简介信息</p>
    </div>
@endif
    <div class="bottom">
        <i></i>
        <span>观看人数：{{$model->views}}</span>
        <button><a href="/strategy/{{$model->id}}">立即阅读</a></button>
    </div>
</div>