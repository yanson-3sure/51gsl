@foreach ($models as $model)
    <li>
        @include('ajax.train.item')
    </li>
@endforeach