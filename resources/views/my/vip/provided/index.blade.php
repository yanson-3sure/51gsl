@extends('layouts.master')
@section('title', '提供的服务')
@section('body-attr', 'class="bd-vip"')
@section('body')
        <!-- 导航tab -->
<div class="weui_navbar" style="position:static">
    <a href="javascript:;" role="trains" class="weui_navbar_item weui_bar_item_on">培训</a>
    <a href="javascript:;" role="strategies" class="weui_navbar_item">策略</a>
    <a href="javascript:;" role="qas" class="weui_navbar_item">问答</a>
</div>


<!-- 培训列表 -->
<div class="tab-change">
    <ul id="trains"></ul>
    <!-- 加载更多按钮 -->
    <div class="load-more-btn" data-url="/my/ajax/train?type=1" data-append-object="#trains">
        <a href="javascript:;" title="">加载更多<i class="icon"></i></a>
    </div>
</div>


<!-- 策略列表 -->
<div class="tab-change hide">
    <ul id="strategies"></ul>
    <!-- 加载更多按钮 -->
    <div class="load-more-btn"  data-url="/my/ajax/strategy?type=1" data-append-object="#strategies">
        <a href="javascript:;" title="">加载更多<i class="icon"></i></a>
    </div>
</div>


<!-- 问答列表 -->
<div class="tab-change hide">
    <div class="sub-nav">
        <div class="subnav-item red">
            <a href="javascript:;" class="mask">已回复</a>
        </div>
        <div class="subnav-item" style="border:none">
            <a href="javascript:;" class="mask">未回复</a>
        </div>
    </div>

    <!-- 已回复 -->
    <ul class="tab-change-msg" id="qa1"></ul>

    <!-- 未回复 -->
    <ul class="tab-change-msg hide" id="qa2"></ul>

    <!-- 加载更多按钮 -->
    <div class="load-more-btn"  data-url="/my/ajax/qa?type=1&role=1" data-append-object="#qa1" >
        <a href="javascript:;" title="">加载更多<i class="icon"></i></a>
    </div>

    <!-- 加载更多按钮 -->
    <div class="load-more-btn hide"  data-url="/my/ajax/qa?type=2&role=1" data-append-object="#qa2" >
        <a href="javascript:;" title="">加载更多<i class="icon"></i></a>
    </div>
</div>
@endsection
@section('footer')
    <script src="/js/jquery.praise.js"></script>
    <script src="/js/jquery.loadmore.js"></script>

    <script>
        $(document).ready(function () {
            $('.load-more-btn[data-append-object!="#qa1"]').loadmore();
            $('.load-more-btn[data-append-object="#qa1"]').loadmore({callback:function(data){
                $('.appreciate').praise();
            }});
            $('.load-more-btn').first().click();
            /* tab-change切换 */
            $('div.weui_navbar a').click(function () {
                var _this = $(this);
                var ind = _this.index();
                $('div.tab-change').hide().eq(ind).show();
                $('div.weui_navbar a').removeClass('weui_bar_item_on').eq(ind).addClass('weui_bar_item_on');
                if($('div.tab-change').eq(ind).find('ul').first().html()=='') {
                    $('div.tab-change').eq(ind).find('.load-more-btn').first().click();
                }
            });

            /* 问答列表切换 */
            $('div.sub-nav div.subnav-item').click(function () {
                var _this = $(this);
                var ind = _this.index();
                $('ul.tab-change-msg').hide().eq(ind).show();
                $('div.sub-nav div.subnav-item').removeClass('red').eq(ind).addClass('red');
                if($('ul.tab-change-msg').eq(ind).html()==''){
                    _this.parent().parent().find('.load-more-btn').eq(ind).click();
                }
            });


        });
    </script>
@endsection
