<span class="left mt18">{{$track->status}}</span>
<div class="right">
    <p class="time">{{ smarty_modifier_time_ago(strtotime($track['updated_at']) )}}</p>
    <p>{{$track->content}}</p>
    @if($track->image)
    <div class="thumbnail">
        <img src="{{getTrackImageUrl($track->image,60)}}" data-big-src="{{getTrackImageUrl($track->image)}}" alt="" height="60px" width="60px">
    </div>
    @endif
</div>