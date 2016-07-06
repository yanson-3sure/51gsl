@foreach($users as $user)
    <li>
    @include('ajax.user.item')
    </li>
@endforeach
