@extends('layouts.master')
@section('title', '培训详细')
@section('html-attr', 'xmlns:gs="http://www.gensee.com/ec"')
@section('body')
        <!-- 视频区 -->
<script type="text/javascript" src="http://static.gensee.com/webcast/static/sdk/js/gssdk.js"></script>

<div class="videowrapper">
    <div id="divVideo" style="width:100%;height:100%">
    <gs:video-live id="videoComponent" site="hxbj.gensee.com" ctx="webcast" ownerid="{{$model->webcastid}}"  uname="{{$user['name']}}"/>
    </div>
    <!-- 视频长宽比预设为16:9 -->
    <a href="/train">
        <img src="../img/zhuye.svg" alt="" width="22px" height="22px">
    </a>
</div>


<!-- 标题栏 -->
<ul class="toggleNav">
    <li role="questions" class="curr">
        <a href="javascript:;" class="mask">问答</a>
    </li>
    <li role="contents" >
        <a href="javascript:;" class="mask">课程目录</a>
    </li>
</ul>

<!-- 问答 -->
<div class="tab-change">
    <ul class="list-box" name="questions" id="questions">
    </ul>
    <!-- 加载更多按钮 -->
    <div class="load-more-btn"  data-url="/qa?object_type=train&object_id={{$model->id}}" data-append-object="#questions">
        <a href="javascript:;" title="">加载更多<i class="icon"></i></a>
    </div>
</div>
<!-- 课程目录 -->
<div class="tab-change hide toggleContent">
    <ul class="review-wrap" name="contents" id="contents" ></ul>
</div>

<!-- 提问栏 -->
<div class="ask-wrap" style="display: none;">
    <form  id="question_form" action="/my/question" method="post">
        <div style="display: none">
            <input type="text" name="object_type" value="train">
            <input type="text" name="object_id" value="{{$model->id}}">
        </div>
        <textarea name="content" placeholder="描述您的问题并以“？”结尾。您可以在VIP服务中查看您的问题"></textarea>
        <input type="submit" value="提问" class="fasong">
    </form>
</div>
@endsection
@section('footer')
    <script src="/js/jquery.praise.js"></script>
    <script src="/js/jquery.loadmore.js"></script>

    <script>
        window.onload = function(){
            // 设置视频高度
            var wid = Math.min(window.screen.width,640);
            var h = wid*9/16;
            $(".videowrapper").css("height",h + "px");
        }

        var viewVideo = function(id){
            $('#divVideo').html('');
            $('#divVideo').html('<gs:video-vod id="videoComponent1" site="hxbj.gensee.com" ctx="webcast" ownerid="'+id+'"  uname="{{$user['name']}}" py="1"/>');
            GS.loadTag('video-vod', document.getElementsByTagName("gs:video-vod")[0]);
        }
        $(function(){
            // 设置视频高度
            var wid = Math.min(window.screen.width,640);
            var h = wid*9/16;
            $(".videowrapper").css("height",h + "px");

            //加载问答
            {{--$.get('/qa?object_type=train&object_id={{$model->id}}',function(data){--}}
                {{--$('#questions').append(data.content);--}}
                {{--$('.appreciate').praise();--}}
            {{--});--}}

            $('.load-more-btn').loadmore({callback:function(data){
                $('.appreciate').praise();
            }});
            $('.load-more-btn').first().click();

            //tab切换
             $("ul.toggleNav li").click(function(){
             	if($(this).attr("class") != "curr"){
                    var _this = $(this);
                    var ind = _this.index();
                    $('ul.toggleNav li').removeClass('curr').eq(ind).addClass('curr');
                    $('div.tab-change').hide();//.eq(ind).show();
                    var name = $(this).attr("role");
                    $("div.tab-change").each(function(){
                        console.log($(this).find('ul').attr("name"));
                        if($(this).find('ul').attr("name") == name) {
                            $(this).show();
                            if ($(this).find('ul').attr("name") == 'contents') {
                                if ($('#contents li').length == 0) {
                                    $.get('/my/video/list?id={{$model->id}}', function (data) {
                                        $('#contents').append(data);
                                    });
                                }
                            }
                        }
                    });
             	}
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
