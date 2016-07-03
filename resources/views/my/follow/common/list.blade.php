@foreach($following_users as $f_user)
    <li>
        @include('my.follow.common.item')
    </li>
@endforeach