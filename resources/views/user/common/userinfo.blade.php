@if(isset($user))
    <input type="hidden" id="uid" value="{{ $user['id']  }}">
    <input type="hidden" id="name" value="{{ $user['name']  }}">
    <input type="hidden" id="avatar" value="{{ getAvatar($user['avatar'])  }}">
@endif