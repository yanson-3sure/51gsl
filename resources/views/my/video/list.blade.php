@foreach($models as $model)
    <li>
        <span class="name">{{$model->subject}}</span>
        <span class="time">{{date('Y-m-d',strtotime($model->createdTime))}}</span>
        <button><a href="javascript:viewVideo('{{$model->id}}')">回看</a></button>
    </li>
@endforeach