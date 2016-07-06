<span class="left mt18">{{$track->status}}</span>
<div class="right">
    <p class="time">{{ smarty_modifier_time_ago(strtotime($track['updated_at']) )}}</p>
    <p>{{$track->content}}</p>
    @if($track->image)
        <a href="{{getTrackImageUrl($track->image)}}" class="thumbnail">
            <img src="{{getTrackImageUrl($track->image,60)}}"  alt="" height="60px" width="60px">
        </a>
    @endif
</div>