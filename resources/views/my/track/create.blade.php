@extends('layouts.master')
@section('title', '创建跟踪内容')
@section('body-attr', 'class="bd-update"')
@section('body')
    <div class="btn-wrap rel">
        <div class="weui_btn weui_btn_mini weui_btn_default btn cancel">取消</div>
        <div class="weui_btn weui_btn_mini btn send" id="sendMsg">发送</div>
    </div>


    <div class="pl10 pr10">
        <form id="myForm" method="post" action="/my/track">
        <h3>{{$strategy->title}}</h3>
        <input type="hidden" name="strategy_id" id="strategy_id" value="{{$strategy->id}}" >

            <div class="status">
            状态：
            <select name="status">
                <option value="">跟踪信息状态</option>
                <option value="关注">关注</option>
                <option value="买入">买入</option>
                <option value="离场">离场</option>
            </select>
        </div>
        <!-- 正文 -->
        <p>当跟踪信息内存在荐股类信息时请更改此状态</p>
        <textarea placeholder="请输入" role="content" name="content"></textarea>
        <input type="hidden" name="image" id="image" value="" >

        <!-- 上传图片 -->
        <div class="weui_uploader_bd mb30" data-url="/track-upload?type=track">
            <ul class="weui_uploader_files" style="display: none;">
                <li class="weui_uploader_file"></li>
            </ul>
            <div class="weui_uploader_input_wrp">
                <input type="file" name="file" class="weui_uploader_input" accept="image/jpg,image/jpeg,image/png,image/gif">
            </div>
        </div>
        </form>
    </div>
@endsection
@section('footer')
    @parent
    <script src="/js/jquery.ui.widget.js"></script>
    <script src="/js/jquery.fileupload.js"></script>

    <script>
        $(function(){
            $('#sendMsg').click(function(){
                layer.load();
                $('#myForm').ajaxSubmit({
                    success: function(data) {
                        //layer.msg(data.result,function(){location.href="/strategy/{{$strategy->id}}";});
                        layer.closeAll();
                        weDialog.saveOk(function(){location.href="/strategy/{{$strategy->id}}";});
                    }
                });
            });
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


