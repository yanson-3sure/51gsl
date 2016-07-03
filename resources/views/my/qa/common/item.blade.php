@if(isset($question->answer))
<div class="queswrap">
    <p class="question">{{$question->content}}</p>
    <div>
        <div class="head-thumb" style="background-image:url({{getAvatar($question->answer->user['avatar'])}})"></div>
        <span>{{$question->answer->user['name']}}</span>
        <div class="appreciate appr" status="{{$question->answer->is_praise ? '1' : '0'}}" data-object-id="{{$question->answer->id}}" data-object-type="answer">
                        <span class="like">
                            <img src="/img/heart.svg">
                        </span>
            <strong>{{$question->answer->praises}}</strong>
        </div>
    </div>
    <p class="answer">
        {{$question->answer->content}}
    </p>
</div>
@else
<div class="weihuifu">
    <div class="left-wrap">
        <p class="f16">{{$question->content}}</p>
        <p class="999">{{ smarty_modifier_time_ago(strtotime($question['created_at']) )}}
            <span class="pdl10">来自
                @if($question->object_type=='train')
                    培训
                @elseif($question->object_type=='strategy')
                    策略
                @endif
            </span>
        </p>
    </div>
    <a href="/my/answer/create?question_id={{$question->id}}" class="r">回答</a>
</div>
@endif