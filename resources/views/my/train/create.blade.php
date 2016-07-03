@extends('layouts.master')
@section('title', '创建培训内容')
@section('body-attr', 'class="newstrategy"')
@section('body')
    <div class="dv-container">
        <div class="btn-wrap rel">
            <div class="weui_btn weui_btn_mini weui_btn_default quxiao">取消</div>
            <div class="weui_btn weui_btn_mini wancheng r" id="sendMsg">发送</div>
        </div>
        <form id="myForm" method="post" action="/my/train">
            <!-- 标题 -->
            <input type="text" placeholder="请输入标题" name="title" class="title">
            <select name="uid" >
                <option value="0">请选择分析师</option>
                @foreach($analysts as $k => $v)
                    <option value="{{$k}}">{{$v['name']}}</option>
                @endforeach
            </select>
            <input type="text" placeholder="培训时间" name="time">
            <input type="text" placeholder="价格" name="price">
            <input type="radio" value="0" name="vip" id="free" checked>
            <label for="free">免费</label>
            <input type="radio" value="1" name="vip" id="vip">
            <label for="vip">VIP</label>

            <!-- 培训内容 -->
            <textarea placeholder="培训内容" role="disc" name="content"></textarea>
            <input type="hidden" name="image" id="image" value="" >

            <!-- 上传图片 -->
            <div class="weui_uploader_bd mb10" data-url="/train-upload?type=train">
                <ul class="weui_uploader_files" style="display: none;">
                    <li class="weui_uploader_file"></li>
                </ul>
                <div class="weui_uploader_input_wrp">
                    <input type="file" name="file" class="weui_uploader_input" accept="image/jpg,image/jpeg,image/png,image/gif">
                </div>
            </div>

            <input type="text" placeholder="展示互动直播ID" name="webcastid">
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
                layer.msg('发送中...');
                $('#myForm').ajaxSubmit({
                    success: function(data) {
                        layer.msg(data.result,function(){location.href="/my";});
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


