@extends('layouts.master')
@section('title', '回答问答')
@section('body-attr', 'class="bd-reply"')
@section('body')
    <div class="btn-wrap rel">
        <a href="/my/vip/provided"  class="weui_btn weui_btn_mini weui_btn_default quxiao">取消</a>
        <div class="weui_btn weui_btn_mini send r"  id="sendMsg">发送</div>
    </div>
    <form id="myForm" method="post" action="/my/answer">
        <p>问题:{{$question->content}}</p>
        <textarea placeholder="请输入" name="content" id="content"></textarea>
        <input type="hidden" name="image" id="image" value="" >
        <input type="hidden" name="question_id" value="{{$question->id}}">

        <!-- 上传图片 -->
        <div class="weui_uploader_bd mb30" data-url="/answer-upload?type=answer">
            <ul class="weui_uploader_files" style="display: none;">
                <li class="weui_uploader_file" ></li>
            </ul>
            <div class="weui_uploader_input_wrp">
                <input type="file" name="file" class="weui_uploader_input" accept="image/jpg,image/jpeg,image/png,image/gif">
            </div>
        </div>
    </form>

@endsection
@section('footer')
    @parent
    <script src="/js/jquery.ui.widget.js"></script>
    <script src="/js/jquery.fileupload.js"></script>
    <script>
        $(function(){
            $('#sendMsg').click(function(){
                if($('#content')==""){
                    layer.msg("发表内容不能为空");
                    return false;
                }
                layer.load();
                $('#myForm').ajaxSubmit({
                    success: function(data) {
                        layer.closeAll();
                        weDialog.sendOk(function(){location.href="/my/vip/provided?show=2";});
                    }
                });
            });
            //http://laravel-media-upload.triasrahman.com/
            var settings = {
                autoUpload : true,
                add : function (e, data) {
                    data.submit();
                },
                done : function (e, data) {
                    if(data.result.error) {
                        layer.msg('ERROR:'+data.result.error);
                        return false;
                    }
                    $(this).find('.weui_uploader_files').show().find('.weui_uploader_file').css('background-image','url('+data.result.path+'@88w_88h)');
                    $('#image').val(data.result.name);
                    layer.msg('上传成功');
                },
            }

            $('.weui_uploader_bd').fileupload(settings);
        });
    </script>
@endsection