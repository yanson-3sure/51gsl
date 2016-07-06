@foreach($tracks as $track)
<li>
    @include('ajax.track.item')
</li>
@endforeach