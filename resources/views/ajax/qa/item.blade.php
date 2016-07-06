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