@foreach($messages as $message)
    <li>
        <!-- 消息框:主贴 -->
        <div class="msg-container" data-id="{{$message['id']}}" @if(!isAdmin($message['from_user']['id']) && !isAnalyst($message['from_user']['role'])) data-user-id="{{$message['from_user']['id']}}" @endif>
            <div class="header">
                <div class="pub-header" style="background-image:url({{ getAvatar($message['from_user']['avatar'])  }})">
                    @if(isAnalyst($message['from_user']['role']))
                    <a href="/user/{{$message['from_user']['id']}}"></a>
                    @endif
                </div>
            </div>
            <div class="body">
                <p class="pub-name">
                    @if(isAnalyst($message['from_user']['role']))
                        <a href="/user/{{$message['from_user']['id']}}">{{$message['from_user']['name']}}</a>
                    @else
                        {{$message['from_user']['name']}}
                    @endif
                </p>
                <p>
                    @if($message['event_type']=='praise:status')
                        <a href="/status/{{$message['event_id']}}">
                        赞
                        </a>
                    @elseif($message['event_type']=='praise:answer')
                        赞
                    @elseif($message['event_type']=='comment')
                        @if($message['event']['object_type']=='status')
                            @if(isset($message['event']['object']['id']))
                                <a href="/status/{{$message['event']['object_id']}}">
                                    @if(isset($message['event']['reply_comment']) && $message['event']['reply_comment']['uid']!=$uid)
                                        回复
                                        <span>{{ $message['event']['reply_comment']['user']['name'] }}</span>
                                    @endif
                                    {{ $message['event']['comment'] }}
                                </a>
                            @else
                                已经删除
                            @endif
                        @endif
                    @endif
                </p>
                <p class="ddd">{{smarty_modifier_time_ago(strtotime($message['created_at']))}}</p>
            </div>
            <div class="right-side">
                @if($message['event_type']=='praise:status')
                    @if(isset($message['event']['image']))
                        <a class="thumbnail" href="{{getStatusImageUrl($message['event']['image'])}}">
                            <img src="{{getStatusImageUrl($message['event']['image'],100)}}" alt="" height="60px" width="60px">
                        </a>
                    @else
                        <a href="/status/{{$message['event_id']}}">
                        <p class="content-wrap">{{ showMsgNoHtml($message['event']['message'])  }}</p>
                        </a>
                    @endif
                @elseif($message['event_type']=='praise:answer')
                    <p class="content-wrap">{{ showMsgNoHtml($message['event']['content'])  }}</p>
                @elseif($message['event_type']=='comment')
                    @if($message['event']['object_type']=='status')
                        @if(isset($message['event']['object']['id']))
                            @if(isset($message['event']['object']['image']))
                                <a href="{{getStatusImageUrl($message['event']['object']['image'])}}" class="thumbnail">
                                    <img src="{{getStatusImageUrl($message['event']['object']['image'],100)}}">
                                </a>
                            @else
                                <a href="/status/{{$message['event']['object']['id']}}">
                                <p class="content-wrap">
                                    {{ showMsgNoHtml($message['event']['object']['message'])  }}
                                </p>
                                </a>
                            @endif
                        @else
                            <p class="content-wrap">
                            已经删除
                            </p>
                        @endif
                    @endif
                @endif
            </div>
        </div>
    </li>
@endforeach