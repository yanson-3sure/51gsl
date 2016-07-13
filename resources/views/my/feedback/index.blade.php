@extends('layouts.master')
@section('title', '客服反馈')
@section('body-attr', 'class="kefu"')
@section('body')
    <div class="container">
        <div class="head" style="background-image:url(/img/1.jpg)"></div>
        <div class="dialog">
            <p>
                如果您在使用过程中遇到任何疑问，请在此处留言，我们将会在3~5天内对您进行回访。为保证我们可以更好的了解到您的问题和及时回访。
                <br/>请按照以下格式：问题（咨询）内容+联系方式+称呼
                <br/>如：策略如何购买季度版？13244647010，李先生
                <br/>产品问题您也可以了联系我们客服客服qqxxxxxxxx   xxxxxxxxxx客服qqxxxxxxxx   xxxxxxxxxx
                <br/>工作时间：
                <br/>周一至周五：9：00-18：00
            </p>
            <span class="time">2016-6-8 11:11</span>
        </div>
        <div style="clear:both"></div>
    </div>
    @foreach($models as $model)
    <div class="container">
        <div class="head" style="background-image:url({{getAvatar($avatar,50)}})"></div>
        <div class="dialog">
            <p>
                {{$model->content}}
            </p>
            <span class="time">{{date('Y-m-d h:m',strtotime($model->created_at))}}</span>
        </div>
        <div style="clear:both"></div>
    </div>
    @endforeach
    <form id="myForm" method="post" action="/my/feedback">
    <div class="content">
        <textarea placeholder="在此输入您的问题，或者联系我们的客服QQ"  name="content" id="content"></textarea>
        <div class="weui_btn weui_btn_warn fasong">发送</div>
    </div>
    </form>

@endsection
@section('footer_nav')
@endsection
@section('footer')
    <script>
        $(function(){
            $('.fasong').click(function(){
                if($('#content')==""){
                    layer.msg("发表内容不能为空");
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