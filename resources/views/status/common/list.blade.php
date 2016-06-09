@foreach ($statuses as $model)
    <li>
        @include('status.common.item')
    </li>
@endforeach