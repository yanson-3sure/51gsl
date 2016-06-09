<div class="con">
    <div class="gz">
        <div class="yh">
            <div class="yhimg">
                <div><a href="{{ url('/analyst/'.$model['uid'])  }}"><img src="{{  getAvatar($model['user']['avatar']) }}"/></a>
                </div>
            </div>
            <div class="use">
                <div class="usename">
                    <span>{{  $model['user']['name'] }}</span>

                    <p class="pub-time">{{ smarty_modifier_time_ago(strtotime($model['created_at']) ) }}</p>
                </div>
            </div>
        </div><!--用户END-->

        @if(isset($model['forward_id']) && $model['forward_id']>0)
            @if($model['forward_type']=='comment' && isset($model['forward']['reply_comment']))
            <a href="{{ url('/status/'.$model['id'])  }}">
                <div class="huifu fans"><span class="blue_1">回复</span>
                    <span>{{$model['message']}}</span><span>//</span>
                    <span class="blue_1 cursor"> {{ $model['forward']['reply_comment']['user']['name']}}</span>
                    <span>:</span><span>{{ $model['forward']['reply_comment']['comment']}}</span>
                </div>
            </a>
            @elseif($model['forward_type']=='status')
            <a href="{{ url('/status/'.$model['id'])  }}">
                <div class="fx_content fans ">{!!nl2p($model['message']) !!}</div>
            </a>
            @endif
        @else
            <a href="{{ url('/status/'.$model['id'])  }}">
                <div class="fx_content fans ">{!!nl2p($model['message']) !!}</div>
            </a>
        @endif
        @if(isset($model['image']))
            <div class="neirong-img">
                <a href="{{getImageUrl($model['image'],0)}}" class="fancybox-effects">
                    <img src="{{getImageUrl($model['image'],88)}}"/>
                </a>
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
                        <a href="{{getImageUrl($model['forward']['image'],0)}}" class="fancybox-effects">
                        <img src="{{getImageUrl($model['forward']['image'],88)}}">
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
                            <a href="{{getImageUrl($model['forward']['object']['image'],0)}}" class="fancybox-effects">
                                <img src="{{getImageUrl($model['forward']['object']['image'],88)}}">
                            </a>
                        @endif
                    </div>
                </a>
                @endif
            @endif
         @endif
                    <!--内容END-->
    </div>
    <div class="hr_1"></div>
    <div class="gz">
        <div class="fenxiang">
            <div class="fenxiang-zan">
                <a href="javascript:;" class="btn_praise" data-object-id="{{$model['id']}}" data-object-type="status">
                        <span {{ $model['praises_count'] >0 ? array_key_exists($uid,$model['praises']) ? 'class=red' : '' : ''}}>
                            <i id="icon_p_{{$model['id']}}" class="icon-heart{{ $model['praises_count'] >0 ? '' : '-empty'}}"></i>赞<span>{{ $model['praises_count'] >0 ? $model['praises_count'] : "" }}</span>
                        </span>
                </a>
            </div>
            <div>|</div>
            <div class="fenxiang-pl" data-object-id="{{$model['id']}}">
                <a href="javascript:;" >
                    <span>
                        <i class=" icon-comment-alt"></i>评论
                        <span id="plcount_{{$model['id']}}">{{ $model['comments_count']>0 ? $model['comments_count'] : ''  }}</span>
                    </span>
                </a>
            </div>
        </div>
        <div class="zan">
            <div id="zan-l-{{$model['id']}}"
                 class="zan-l" {{$model['praises_count'] >0 ? '':'style=display:none'}}>
                <span class="red"><i class="icon-heart-empty"></i></span></div>
            <div class="zan-r" id="zan-r_{{$model['id']}}">
                @if(array_key_exists('praises',$model))
                    @foreach($model['praises'] as $k => $v)
                        <a href="javascript:;">
                            <div class="zan-r-a" id="praise_{{$model['id']}}_{{ $k  }}">
                                <img width="28px" height="28px" src="{{ getAvatar($v['avatar'],28) }}"/>
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>
        </div><!--赞END-->
        <div class="pinglun" id="pinglun_{{$model['id']}}">
            @if(array_key_exists('comments',$model))
                @foreach($model['comments'] as $k => $v)
                    <p class="reply_comment" data-object-id="{{$model['id']}}" data-reply-id="{{$k}}">
                        <a href="javascript:;">{{ $v['user']['name']  }}</a>
                        @if(isset($v['reply_user']['name']))
                            <span>回复</span><a href="javascript:;"> {{$v['reply_user']['name']}}</a>
                        @endif
                        :<span>{{ $v['comment'] }}</span>
                    </p>
                @endforeach
            @endif
        </div><!--评论END-->
    </div>
    @if(!isset($detail))
        <div class="hr_2"></div>
    @endif
</div>