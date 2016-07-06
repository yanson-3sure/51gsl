@extends('layouts.master')
@section('title', '名师排行')
@section('body-attr', 'class="home"')
@section('body')
        <!-- 顶部导航tab -->
<div class="weui_navbar">
    <a href="{{url('/home?type=home')}}" class="weui_navbar_item">关注</a>
    <a href="{{url('/')}}" class="weui_navbar_item">全部</a>
    <a href="javascript:;" class="weui_navbar_item weui_bar_item_on">名师</a>
</div>

<!-- "名师"列表 -->

<div class="sub-nav">
    <div class="subnav-item subnav-item-on">直播最多</div>
    <div class="subnav-item">人气最高</div>
    <div class="subnav-item">互动最多</div>
</div>

<!-- 直播最多 -->
<div class="tab-change">
    <ul id="list1"></ul>
    <!-- 加载更多按钮 -->
    <div class="load-more-btn hide" data-url="/ajax/user?type=1" data-append-object="#list1">
        <a href="javascript:;" title="">加载更多<i class="icon"></i></a>
    </div>
</div>

<!-- 人气最高 -->
<div class="tab-change hide">
    <ul id="list2"></ul>
    <!-- 加载更多按钮 -->
    <div class="load-more-btn hide" data-url="/ajax/user?type=2" data-append-object="#list2">
        <a href="javascript:;" title="">加载更多<i class="icon"></i></a>
    </div>
</div>
<div class="tab-change hide">
    <ul id="list3"></ul>
    <!-- 加载更多按钮 -->
    <div class="load-more-btn hide" data-url="/ajax/user?type=3" data-append-object="#list3">
        <a href="javascript:;" title="">加载更多<i class="icon"></i></a>
    </div>
</div>
@endsection
@section('footer')
    <script src="/js/jquery.loadmore.js"></script>
    <script src="/js/jquery.follow.js"></script>
    <script>
        $(function () {
            var callback =function(data){
                $('.guanzhu').follow();
            }
            callback();
            $('.load-more-btn').loadmore({callback:function(data){
                callback(data);
            }});
            $('.load-more-btn').first().click();

            /* 问答列表切换 */
            $('div.sub-nav div.subnav-item').click(function () {
                var _this = $(this);
                var ind = _this.index();
                $('div.tab-change').hide().eq(ind).show();
                $('div.sub-nav div.subnav-item').removeClass('subnav-item-on').eq(ind).addClass('subnav-item-on');
                if($('div.tab-change').eq(ind).find('ul').html()==''){
                    $('div.tab-change').eq(ind).find('.load-more-btn').click();
                }
            });
        });
    </script>
@endsection