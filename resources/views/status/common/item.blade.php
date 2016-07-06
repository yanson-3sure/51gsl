<!-- 消息框:主贴 -->
<div class="msg-container" data-id="{{$model['id']}}">
    <div class="header">
        <div class="pub-header" style="background-image:url({{ getAvatar($model['user']['avatar'],88) }})">
            <a href="{{ url('/user/'.$model['uid'])  }}"></a>
        </div>
        <p class="pub-name">
            <a href="{{ url('/user/'.$model['uid'])  }}">{{  $model['user']['name'] }}</a>
        </p>
        <p>{{ smarty_modifier_time_ago(strtotime($model['created_at']) ) }}</p>
        @if(isAdmin($uid) || $uid==$model['uid'])
        <i class="handle" ></i>
        @endif
    </div>
    <div class="body">
        <a href="{{ url('/status/'.$model['id'])  }}">
        @if(isset($model['forward_id']) && $model['forward_id']>0)
            @if($model['forward_type']=='comment')
                @if(isset($model['forward']['reply_comment']))
                    <p>
                        <span></span>{{$model['message']}}
                        <span>//</span><span class="red">{{ $model['forward']['reply_comment']['user']['name']}}</span>
                        <span>:</span>{{ $model['forward']['reply_comment']['comment']}}
                    </p>
                @else
                    <p>{!!nl2p($model['message']) !!}</p>
                @endif
            @elseif($model['forward_type']=='status')
                <p>{!!nl2p($model['message']) !!}</p>
            @else
                <p>{!!nl2p($model['message']) !!}</p>
            @endif
        @else
            <p>{!!nl2p($model['message']) !!}</p>
        @endif
        </a>
        @if(isset($model['image']))
        <a href="{{getStatusImageUrl($model['image'])}}"  class="thumbnail">
            <img class="lazy" src="{{getStatusImageUrl($model['image'],100)}}" alt="" height="60px" width="60px">
        </a>
        @endif
        @if(isset($model['forward_id']) && $model['forward_id']>0 && $model['forward'])
            @if($model['forward_type']=='status')
                <a href="{{ url('/status/'.$model['forward']['id'])  }}">
                    <a class="content-wrap">
                        <p>
                            <a href="/status/{{$model['forward_id']}}">
                                <span class="red">{{ $model['forward']['user']['name']}}</span>：{!! showMsg($model['forward']['message'])!!}
                            </a>
                        </p>
                        @if(isset($model['forward']['image']))
                            <a href="{{getImageUrl($model['forward_type'],$model['forward']['image'])}}" class="thumbnail">
                            <img src="{{getImageUrl($model['forward_type'],$model['forward']['image'],60)}}" alt="" height="60px" width="60px">
                        </a>
                        @endif
                    </div>
                </a>
            @elseif($model['forward_type']=='comment' && isset($model['forward']['object']))
                @if($model['forward']['object_type']=='status')
                    @if(isset($model['forward']['object']['id']))
                    <a href="{{ url('/status/'.$model['forward']['object']['id'])  }}">
                        <div class="content-wrap">
                            <p>
                                <a href="/status/{{$model['forward']['object']['id']}}">
                                    <span class="red">{{ $model['forward']['object']['user']['name']}}</span>：{!! showMsg($model['forward']['object']['message'])!!}
                                </a>
                            </p>
                            @if(isset($model['forward']['object']['image']))
                                <a href="{{getStatusImageUrl($model['forward']['object']['image'])}}" class="thumbnail">
                                    <img src="{{getStatusImageUrl($model['forward']['object']['image'],60)}}" alt="" height="60px" width="60px" >
                                </a>
                            @endif
                        </div>
                    </a>
                    @else
                    <div class="content-wrap">
                        <p>已经删除</p>
                    </div>
                    @endif
                @endif
            @elseif($model['forward_type']=='strategy')
                <a href="{{ url('/strategy/'.$model['forward_id'])  }}">
                    <div class="content-wrap">
                        <a href="/strategy/{{$model['forward_id']}}" class="mask">
                            @if(isset($model['forward']['vip']) && $model['forward']['vip'])
                                <span class="tip">VIP</span>
                            @endif
                            <span>{{$model['forward']['title']}}</span>
                            <p>摘要：{{$model['forward']['intro']}}</p>
                        </a>
                    </div>
                </a>
            @endif
        @endif
    </div>
    <div class="footer">
        <!-- 点赞与评论按钮 -->
        <div class="comment-wrap">
            <div class="appreciate" status="{{ $model['praises_count'] >0 ? array_key_exists($uid,$model['praises']) ? '1' : '0' : '0'}}" data-object-id="{{$model['id']}}" data-object-type="status">
                <span class="like"><img src="/img/heart.svg"></span>
                <strong>{{ $model['praises_count'] >0 ? $model['praises_count'] : "" }}</strong>
            </div>
            <div class="make-comment" data-object-id="{{$model['id']}}">
                <img src="/img/comment.svg" width="20px">
                <strong id="plcount_{{$model['id']}}">{{ $model['comments_count']>0 ? $model['comments_count'] : ''  }}</strong>
            </div>
        </div>
        <!-- 赞 -->
        <div class="reviewers" {{$model['praises_count'] >0 ? '':'style=display:none'}}>
            @if(array_key_exists('praises',$model))
                @foreach($model['praises'] as $k => $v)
                    <img id="praise_{{$model['id']}}_{{ $k  }}" src="{{ getAvatar($v['avatar'],44) }}"/>
                @endforeach
            @endif
        </div>
        <!-- 评论区 -->
        <div class="comment-content" id="comment_{{$model['id']}}">
            @if(array_key_exists('comments',$model))
                @foreach($model['comments'] as $k => $v)
                    <p data-object-id="{{$model['id']}}" data-reply-id="{{$k}}">
                        <span>{{ $v['user']['name']  }}</span>
                        @if(isset($v['reply_user']['name']))
                            <span>回复</span><span>{{$v['reply_user']['name']}}</span>
                        @endif
                        :{{ $v['comment'] }}
                    </p>
                @endforeach
            @endif
        </div>
    </div>
</div>