@if($isLogin)
        <!--评论-->
<div class="plk" id="plk" style="display:none">
    <form id="comment_form" action="/my/comment" method="post">
        <div style="display: none">
        <input type="text" id="comment_object_id" name="comment_object_id" value="0">
        <input type="text" id="reply_comment_id" name="reply_comment_id" value="0">
        <input type="text" id="comment_object_type" name="comment_object_type" value="{{ $object_type or 'status'}}">
        </div>
        <div class="plk_nav">
            <div class="plk_qx plk_butt">取消</div>
            <div class="plk_pl plk_butt">确定</div>
        </div>
        <p class="emoji-picker-container">
            <textarea class="form-control textarea-control" rows="3" placeholder="" id="comment_body"
                      name="comment_body"></textarea>
        </p>
        <div class="plk_fot">
            @if(isAnalyst($role))
                <div class="plk-zf"><input type="checkbox" value="on" name="r_comment" id="r_comment"/><a href="#" class="gray">同时转发</a>
                </div>
            @endif
        </div>
    </form>
</div>

@endif