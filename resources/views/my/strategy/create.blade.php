@extends('layouts.master')
@section('title', '创建策略内容')
@section('body-attr', 'class="newstrategy"')
@section('head')
    @include('UEditor::head')
@endsection
@section('body')
    <div class="dv-container">
        <div class="btn-wrap rel">
            <div class="weui_btn weui_btn_mini weui_btn_default quxiao">取消</div>
            <div class="weui_btn weui_btn_mini wancheng r" id="sendMsg">发送</div>
        </div>
        <form id="myForm" method="post" action="/my/strategy">
        <!-- 标题 -->
        <input type="text" placeholder="请输入标题" name="title" class="title">
        <input type="radio" value="0" name="vip" id="free" checked>
        <label for="free">免费</label>
        <input type="radio" value="1" name="vip" id="vip">
        <label for="vip">VIP</label>

        <!-- 摘要 -->
        <textarea placeholder="摘要" role="disc" name="intro"></textarea>

        <!-- 正文 -->
        <h3>正文内容</h3>
            <!-- 加载编辑器的容器 -->
            <script id="container" name="content" type="text/plain"></script>
        {{--<textarea placeholder="请输入" role="content" name="content"></textarea>--}}

        <input type="text" placeholder="风险提示：不可为空" name="risk" maxlength="40">
        </form>
    </div>
@endsection
@section('footer')
    @parent
    <script src="/js/jquery.ui.widget.js"></script>
    <script src="/js/jquery.fileupload.js"></script>
    <!-- 实例化编辑器 -->
    <script type="text/javascript">
        var ue = UE.getEditor('container',{toolbars: []});
        ue.ready(function() {
            ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');
            ue.setContent('');
        });
    </script>
    <script>
        $(function(){
            $('#sendMsg').click(function(){
                if(ue.getContent()==""){
                    layer.msg("策略内容不能为空");
                    return false;
                }
                layer.load();
                $('#myForm').ajaxSubmit({
                    success: function(data) {
                        layer.closeAll();
                        weDialog.sendOk(function(){location.href="/my";});
                    }
                });
            });
        });
    </script>
@endsection


