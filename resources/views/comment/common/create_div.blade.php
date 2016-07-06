@if($isLogin)
<!-- 评论输入框 -->
<div class="mask" id="plk" style="display:none">
    <div class="comment-box">
        <button class="weui_btn weui_btn_default cancel">取消</button>
        <button class="weui_btn weui_btn_warn send">发送</button>
        <form id="comment_form" action="/my/comment" method="post">
            <div style="display: none">
                <input type="text" id="comment_object_id" name="comment_object_id" value="0">
                <input type="text" id="reply_comment_id" name="reply_comment_id" value="0">
                <input type="text" id="comment_object_type" name="comment_object_type" value="{{ $object_type or 'status'}}">
            </div>
        <textarea id="comment_body" name="comment_body"></textarea>
            @if(isAnalyst($role))
        <div class="r">
            <input type="checkbox" class="vm" value="on" name="r_comment" id="r_comment"> 同时转发
        </div>
            @endif
        </form>
    </div>
</div>
@endif