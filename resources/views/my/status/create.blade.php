@extends('layouts.master')
@section('title', '创建文章内容')
@section('body-attr', 'class="bd-sendmsg"')
@section('body')
    <div class="btn-wrap rel">
        <a href="/my"  class="weui_btn weui_btn_mini weui_btn_default quxiao">取消</a>
        <div class="weui_btn weui_btn_mini weui_btn_primary send" id="sendMsg">发送</div>
    </div>
    <form id="myForm" method="post" action="/my/status">
        @if(isset(config('base.dyxc.users')[$uid]))
            <p>
                <input type='radio' name='NewsType' value='1' checked/>大盘
                <input type='radio' name='NewsType' value='2'/>名家论市
                <input type='radio' name='NewsType' value='3'/>热股
                <input type='radio' name='NewsType' value='4'/>消息
                <input type='radio' name='NewsType' value='5'/>独家
                <input type='radio' name='NewsType' value='6'/>个股关注
                <input type='radio' name='NewsType' value='7'/>绩效回顾
            </p>
        @endif
        <textarea placeholder="请输入" name="message" id="message"></textarea>
        <input type="hidden" name="image" id="image" value="" >

        <!-- 上传图片 -->
        <div class="weui_uploader_bd mb30" data-url="/status-upload?type=status">
            <ul class="weui_uploader_files" style="display: none;">
                <li class="weui_uploader_file" ></li>
            </ul>
            <div class="weui_uploader_input_wrp">
                <input type="file" name="file" class="weui_uploader_input" accept="image/jpg,image/jpeg,image/png,image/gif">
            </div>
            <a href="/my/strategy/create" class="xinzeng">新增策略</a>
        </div>
    </form>

    <!-- 发送成功提示 -->
    <img src="img/2016/yifasong.svg" id="sendSuccess">
@endsection
@section('footer')
    @parent
    <script src="/js/jquery.ui.widget.js"></script>
    <script src="/js/jquery.fileupload.js"></script>
    <script>
        $(function(){
            $('#sendMsg').click(function(){
                if($('#message')==""){
                    layer.msg("发表内容不能为空");
                    return false;
                }
                layer.msg('发送中...');
                $('#myForm').ajaxSubmit({
                    success: function(data) {
                        layer.msg(data.result,function(){location.href="/my";});
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


