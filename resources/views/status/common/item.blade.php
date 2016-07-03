<!-- 消息框:主贴 -->
<div class="msg-container" id="status_{{$model['uid']}}">
    <div class="header">
        <div class="pub-header" style="background-image:url({{ getAvatar($model['user']['avatar'],88) }})">
            <a href="{{ url('/user/'.$model['uid'])  }}"></a>
        </div>
        <p class="pub-name">
            <a href="{{ url('/user/'.$model['uid'])  }}">{{  $model['user']['name'] }}</a>
        </p>
        <p>{{ smarty_modifier_time_ago(strtotime($model['created_at']) ) }}</p>
        <i class="handle"></i>
    </div>
    <div class="body">
        <a href="{{ url('/status/'.$model['id'])  }}">
        @if(isset($model['forward_id']) && $model['forward_id']>0)
            @if($model['forward_type']=='comment')
                @if(isset($model['forward']['reply_comment']))
                    <p>
                        <span>回复</span>{{$model['message']}}
                        <span>//</span><span>{{ $model['forward']['reply_comment']['user']['name']}}</span>
                        <span>:</span>{{ $model['forward']['reply_comment']['comment']}}
                    </p>
                @else
                    <p>{!!nl2p($model['message']) !!}</p>
                @endif
            @elseif($model['forward_type']=='status')
                <p>{!!nl2p($model['message']) !!}</p>
            @endif
        @else
            <p>{!!nl2p($model['message']) !!}</p>
        @endif
        </a>
        @if(isset($model['image']))
        <div class="thumbnail" >
            <img class="lazy" src="{{getStatusImageUrl($model['image'],60)}}" src1="{{getStatusImageUrl($model['image'])}}" alt="" height="60px" width="60px">
        </div>
        @endif


        @if(isset($model['forward_id']) && $model['forward_id']>0 && $model['forward'])
            @if($model['forward_type']=='status')
                <a href="{{ url('/status/'.$model['forward']['id'])  }}">
                    <div class="fx_content1 fans ">
                        {{ $model['forward']['user']['name']}}:
                        {!! showMsg($model['forward']['message'])!!}
                        @if(isset($model['forward']['image']))
                            <br>
                            <a href="{{getImageUrl($model['forward_type'],$model['forward']['image'])}}" class="fancybox-effects">
                                <img src="{{getImageUrl($model['forward_type'],$model['forward']['image'],88)}}">
                            </a>
                        @endif
                    </div>
                </a>
            @elseif($model['forward_type']=='comment' && isset($model['forward']['object']))
                @if($model['forward']['object_type']=='status')
                    <a href="{{ url('/status/'.$model['forward']['object']['id'])  }}">
                        <div class="fx_content1 fans ">
                            {{ $model['forward']['object']['user']['name']}}:
                            {!! showMsg($model['forward']['object']['message'])!!}
                            @if(isset($model['forward']['object']['image']))
                                <br>
                                <a href="{{getImageUrl($model['forward']['object_type'],$model['forward']['object']['image'],0)}}" class="fancybox-effects">
                                    <img src="{{getImageUrl($model['forward']['object_type'],$model['forward']['object']['image'],88)}}">
                                </a>
                            @endif
                        </div>
                    </a>
                @endif
            @endif
        @endif
    </div>
    <div class="footer">
        <!-- 点赞与评论按钮 -->
        <div class="comment-wrap">
            <div class="appreciate" status="{{ $model['praises_count'] >0 ? array_key_exists($uid,$model['praises']) ? '1' : '0' : '0'}}">
                <span class="like"><img src="/img/heart.svg"></span>
                <strong>{{ $model['praises_count'] >0 ? $model['praises_count'] : "" }}</strong>
            </div>
            <div class="make-comment">
                <img src="/img/comment.svg" width="20px">
                <strong>{{ $model['comments_count']>0 ? $model['comments_count'] : ''  }}</strong>
            </div>
        </div>
        <!-- 赞 -->
        <div class="reviewers" id="zan-l-{{$model['id']}}" {{$model['praises_count'] >0 ? '':'style=display:none'}}>
            @if(array_key_exists('praises',$model))
                @foreach($model['praises'] as $k => $v)
                    <img class="lazy" id="praise_{{$model['id']}}_{{ $k  }}" src="{{ getAvatar($v['avatar'],44) }}"/>
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