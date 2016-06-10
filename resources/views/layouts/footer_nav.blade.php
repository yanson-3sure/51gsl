<!--底部导航-->
<div class="footer">
    <a href="{{ $isLogin ? '/home?type=home' : '/home'  }}" class="fot-zb">
        @if(isset($isFirst))
            <div class="img_zb1 img_gy"></div>
            <p class="fot-nav red">直播</p>
            <span class="fot-zb-1 fot" style="display:none" id="numTip">999+</span>
        @else
            <div class="img_zb img_gy"></div>
            <p class="fot-nav">直播</p>
            <span class="fot-zb-1 fot" style="display:none" id="numTip">999+</span>
        @endif
    </a>
    @if(isset($isTrain) )
        <a class="fot-px" href="/train" style="width:34%;">
            <div class="img_px1 img_gy"></div>
            <p class="fot-nav red">培训</p>
        </a>
    @else
        <a class="fot-px" href="/train" style="width:34%;">
            <div class="img_px img_gy"></div>
            <p class="fot-nav">培训</p>
        </a>
    @endif
    @if(isset($isMy))
        <a class="fot-wd" href="{{url('/my')}}">
            <div class="img_wd1 img_gy"></div>
            <p class="fot-nav red">我的</p>
        </a>
    @else
        <a class="fot-wd" href="{{url('/my')}}">
            <div class="img_wd img_gy"></div>
            <p class="fot-nav">我的</p>
        </a>
    @endif
</div>