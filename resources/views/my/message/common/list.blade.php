@foreach($messages as $message)
    <li>
        <a href="{{url('/status/'.$message['status']['statusid'])}}">
            <div class="con">
                <div class="gz">

                    <div class="yh">
                        <div class="xxzx-l"><img src="{{ getAvatar($message['from_user']['avatar'])  }}"></div>
                        <div class="xxzx-c">
                        <a href="{{url('/status/'.$message['status']['statusid'])}}">
                        <p class="xxzx-c-id">

                            @if($message['event_type']=='comment')
                                <span class="fans">{{ $message['from_user']['nickname'] }}</span>
                                <span class="blue">回复
                                    @if(isset($message['body']['reply_comment']) && $message['body']['reply_comment']['userid']!=$userid)
                                        {{ $message['body']['reply_user']['nickname'] }}
                                    @else
                                        我
                                    @endif
                                </span>
                                <span>{{ $message['body']['body']  }}</span>
                            @elseif($message['event_type']=='praise')
                                <sapn class="fans">{{ $message['from_user']['nickname'] }}</sapn>
                                <span class="red"><i class="icon-heart-empty"></i></span>
                            @endif

                        </p>


                        <p class="xxzx-c-time">{{smarty_modifier_time_ago(strtotime($message['created_at']))}}</p>
                        </a>
                        </div>
                        <div class="xxzx-r">
                            @if(isset($message['status']['image']))
                                <a href="{{getImageUrl($message['status']['image'],0)}}" class="fancybox-effects">
                                    <img src="{{getImageUrl($message['status']['image'],66)}}">
                                </a>
                            @else
                                <p>{{ $message['status']['body']  }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="hr-1"></div>
            </div>
        </a>
    </li>
@endforeach