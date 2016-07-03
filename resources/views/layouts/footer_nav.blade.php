<!-- 底部导航栏 -->
<div class="weui_tabbar">
    <a href="{{ $isLogin ? '/home?type=home' : '/home'  }}" class="weui_tabbar_item">
        <div class="weui_tabbar_icon">
            <img src="/img/live{{isset($footer_isFirst) ? '_on' : '' }}.svg">
        </div>
        <p class="weui_tabbar_label"{{isset($footer_isFirst) ? ' style="color:#ff4444"' : '' }}>直播</p>
    </a>
    <a href="/train" class="weui_tabbar_item">
        <div class="weui_tabbar_icon">
            <img src="/img/train{{isset($footer_isTrain) ? '_on' : '' }}.svg">
        </div>
        <p class="weui_tabbar_label"{{isset($footer_isTrain) ? ' style=color:#ff4444' : '' }}>培训</p>
    </a>
    <a href="/strategy" class="weui_tabbar_item">
        <div class="weui_tabbar_icon">
            <img src="/img/strategy{{isset($footer_isStrategy) ? '_on' : '' }}.svg">
        </div>
        <p class="weui_tabbar_label"{{isset($footer_isStrategy) ? ' style=color:#ff4444' : '' }}>策略</p>
    </a>
    <a href="/my" class="weui_tabbar_item">
        <div class="weui_tabbar_icon">
            <img src="/img/mine{{isset($footer_isMy) ? '_on' : '' }}.svg">
        </div>
        <p class="weui_tabbar_label"{{isset($footer_isMy) ? ' style=color:#ff4444' : '' }}>我的</p>
    </a>
</div>