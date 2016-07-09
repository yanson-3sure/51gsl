@extends('layouts.master')
@section('title', '策略详细')
@section('body-attr', 'class="bfff"')
@section('body')

<!-- 策略头部 -->
<div class="stragegy-head">
    <div class="pub-header" style="background-image:url({{getAvatar($model->user['avatar'])}})">
        <a href="/user/{{$model->uid}}"></a>
    </div>
    <h3>@if($model->vip)<img src="/img/vip.svg" width="22px">@endif{{$model->title}}</h3>
    <div>
        <span class="pub-name"><a href="/user/{{$model->uid}}">{{$model->user['name']}}</a></span>
        <span class="999">{{ smarty_modifier_time_ago(strtotime($model['updated_at']) )}}</span>
        <span class="view">阅读：{{$model->views}}</span>
    </div>
    <!-- 对于讲师，显示i隐藏a；对于普通用户，显示a隐藏i; -->
    @if(isAnalyst($user['role']) && $uid == $model->uid)
    <i class="handle"></i>
    @else
    <a href="/user/{{$model->uid}}" class="home" >
        <img src="/img/zhuye.svg"  alt="" width="22px">
    </a>
    @endif
</div>

<!-- 导航tab -->
<div class="weui_navbar" style="position:static">
    <a href="javascript:;" role="tracks" class="weui_navbar_item weui_bar_item_on_other">跟踪</a>
    <a href="javascript:;" role="contents" class="weui_navbar_item">正文</a>
    <a href="javascript:;" role="questions" class="weui_navbar_item">问答</a>
</div>


<!-- 跟踪 -->
<div class="tab-change strategy-content">
    <h3 class="subtitle">策略跟踪</h3>
    <ul class="genzong"></ul>
    <div class="risk">风险提示：{{$model->risk}}</div>
    <!-- 加载更多按钮 -->
    <div class="load-more-btn" data-url="/ajax/track?id={{$model->id}}" data-append-object=".genzong">
        <a href="javascript:;" title="">加载更多<i class="icon"></i></a>
    </div>
</div>


<!-- 正文 -->
<div class="tab-change tab-container strategy-content hide">
    <div class="tab-content" name="contents">
        <h3 class="subtitle">策略正文</h3>
        <div class="zhengwen">
            <p class="time">{{$model['created_at']}}</p>
            <p id="strategy_content"></p>
        </div>
    </div>
</div>


<!-- 问答 -->
<div class="tab-change tab-container strategy-content hide">
    <div class="tab-content" name="questions">
        <h3 class="subtitle">策略问答</h3>
        <ul id="questions"></ul>
        <!-- 加载更多按钮 -->
        <div class="load-more-btn"  data-url="/ajax/qa?object_type=strategy&object_id={{$model->id}}" data-append-object="#questions">
            <a href="javascript:;" title="">加载更多<i class="icon"></i></a>
        </div>
        <!-- 提问栏 -->
        <div class="ask-wrap bot0">
            <form  id="question_form" action="/my/question" method="post">
                <div style="display: none">
                    <input type="text" name="object_type" value="strategy">
                    <input type="text" name="object_id" value="{{$model->id}}">
                </div>
                <textarea name="content" placeholder="描述您的问题并以“？”结尾。您可以在VIP服务中查看您的问题"></textarea>
                <input type="submit" value="提问" class="fasong">
            </form>
        </div>
    </div>
</div>
@if(isAnalyst($user['role']) && $uid == $model->uid)
<!-- 底部弹出式菜单 -->
<div class="container" id="container">
    <div id="actionSheet_wrap">
        <div class="weui_mask_transition" id="mask" style="display:none"></div>
        <div class="weui_actionsheet" id="weui_actionsheet" style="z-index:9999">
            <div class="weui_actionsheet_menu">
                <div class="weui_actionsheet_cell" id="deleteMsg">
                    <a href="/my/track/create?id={{$model->id}}">更新策略</a>
                </div>
                <div class="weui_actionsheet_cell" id="deleteMsg"><a href="/user/{{$model->uid}}">返回主页</a></div>
            </div>
            <div class="weui_actionsheet_action">
                <div class="weui_actionsheet_cell" id="actionsheet_cancel">取消</div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
@section('footer_nav')
@endsection
@section('footer')
    @include('layouts.script_fancybox')
    <script src="/js/jquery.praise.js"></script>
    <script src="/js/jquery.loadmore.js"></script>
    @if(isAdmin($uid) || isAnalyst($role))
        <script src="/js/jquery.actionsheet.js"></script>
    @endif
    <script>
        $(document).ready(function () {
            @if(isAdmin($uid) || isAnalyst($role))
                $('.handle').actionsheet();
            @endif
        {{--var loadTrack =function(){--}}
            {{--$.get('/track?id={{$model->id}}', function (data) {--}}
                {{--$('.genzong').append(data.content);--}}
            {{--});--}}
        {{--}--}}
        $('.load-more-btn').first().loadmore({callback:function(data){
                fancybox();
            }});
            $('.load-more-btn').first().click();
            $('.load-more-btn').last().loadmore({callback:function(data){
                $('.appreciate').praise();
            }});

            /* tab-change切换 */
            $('div.weui_navbar a').click(function () {
                var _this = $(this);
                var ind = _this.index();
                $('div.tab-change').hide().eq(ind).show();
                $('div.weui_navbar a').removeClass('weui_bar_item_on_other').eq(ind).addClass('weui_bar_item_on_other');
                var name = $(this).attr("role");
                $("div.tab-content").each(function(){
                    if($(this).attr("name") == name) {
                        if ($(this).attr("name") == 'contents') {
                            if ($('#strategy_content').html() == '') {
                                $.get('/ajax/strategy/{{$model->id}}', function (data) {
                                    $('#strategy_content').append(data.content);
                                });
                            }
                        }else if ($(this).attr("name") == 'questions') {
                            $('.load-more-btn').last().click();
                        }
                    }
                });
            });
            $('.fasong').click(function(){
                $('#question_form').ajaxForm({
                    success: function (data) {
                        layer.msg('成功');
                        $('#question_form')[0].reset();
                    }
                });
            });
        });
    </script>
@endsection
