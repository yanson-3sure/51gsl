@foreach($messages as $message)
    <li>
            <div class="con">
                <div class="gz">
                    <div class="yh">
                        <div class="xxzx-l"><img src="{{ getAvatar($message['from_user']['avatar'])  }}"></div>
                        @if($message['event_type']=='praise:status')
                            <a href="{{url('/status/'.$message['event_id'])}}">
                            <div class="xxzx-c">
                            <p class="xxzx-c-id">
                                    <sapn class="fans">{{ $message['from_user']['name'] }}</sapn>
                                    <span class="red"><i class="icon-heart-empty"></i></span>
                            </p>
                            <p class="xxzx-c-time">{{smarty_modifier_time_ago(strtotime($message['created_at']))}}</p>

                            </div>
                            <div class="xxzx-r">
                                @if(isset($message['event']['image']))
                                    <a href="{{getImageUrl($message['event']['image'],0)}}" class="fancybox-effects">
                                        <img src="{{getImageUrl($message['event']['image'],66)}}">
                                    </a>
                                @else
                                    <p>{{ $message['event']['message']  }}</p>
                                @endif
                            </div>
                            </a>
                        @elseif($message['event_type']=='comment')
                            @if($message['event']['object_type']=='status')
                            <a href="{{url('/status/'.$message['event']['object']['id'])}}">
                            @endif
                            <div class="xxzx-c">
                                    <p class="xxzx-c-id">
                                        <span class="fans">{{ $message['from_user']['name'] }}</span>
                                        @if(isset($message['event']['reply_comment']) && $message['event']['reply_comment']['uid']!=$uid)
                                            <span class="blue">回复
                                                {{ $message['event']['reply_comment']['user']['name'] }}
                                            </span>
                                        @endif
                                        <span>{{ $message['event']['comment'] }}</span>
                                    </p>
                                    <p class="xxzx-c-time">{{smarty_modifier_time_ago(strtotime($message['created_at']))}}</p>

                            </div>
                            <div class="xxzx-r">
                                @if(isset($message['event']['object']['image']))
                                    <a href="{{getImageUrl($message['event']['object']['image'],0)}}" class="fancybox-effects">
                                        <img src="{{getImageUrl($message['event']['object']['image'],66)}}">
                                    </a>
                                @else
                                    <p>
                                        @if($message['event']['object_type']=='status')
                                            {{ $message['event']['object']['message']  }}
                                        @endif
                                    </p>
                                @endif
                            </div>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="hr-1"></div>
            </div>
    </li>
@endforeach