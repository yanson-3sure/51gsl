(function ($) {
    $.fn.comment = function(options){
        var defaults = {
            comment_div:"#plk",
            comment_form:"#comment_form",
            comment_post : ".comment-box .send",
            comment_cancel:'.comment-box .cancel'
        }
        var options = $.extend(defaults, options);
        var div = $(options.comment_div);
        var form = $(options.comment_form);
        this.each(function(){
           //$(this).click(show(this));
            $(this).bind('click',function(){show(this);});

        });
        var show = function(obj){
            form[0].reset();//重置表单
            var object_id = $(obj).attr('data-object-id');
            $('#comment_object_id').val(object_id);
            var data_reply_id = $(obj).attr('data-reply-id');
            if(data_reply_id){
                $("#reply_comment_id").val(data_reply_id);
            }
            var data_object_type = $(obj).attr('data-object-type');
            if(data_object_type){
                $("#comment_object_type").val(data_object_type);
            }
            // 显示评论框
            div.fadeIn().find(".comment-box").css("top",0).animate({"top":"50px"}).find("textarea")
                .focus();
            var data_reply_name = $(obj).attr('data_reply_name');
            if(data_reply_name){
                div.attr("placeholder","回复：" + data_reply_name);
            }
        }
        var btn_post = $(options.comment_post);
        var btn_cancel = $(options.comment_cancel);
        if($._data(btn_post[0], 'events' )){
            return ;
        };
        btn_post.click(function(){
            form.ajaxSubmit({
                success: function(data) {
                    var object_id = $('#comment_object_id').val();
                    var name = data.name;
                    var reply_nickname = data.reply_name ? data.reply_name : '';
                    var content = '<p  data-object-id="'+object_id+'" data-reply-id="'+data.comment_id+'">';
                    content += "<span>"+name + '</span>:';
                    if(reply_nickname!=''){
                        content += '<span>回复</span> ' + "<span>" + reply_nickname + "</span>:";
                    }
                    content += ""+data.comment+"";
                    content +='</p>';
                    //console.log(content);
                    $('#comment_'+object_id).append(content);
                    var count = $('#plcount_'+object_id).text();
                    if(count==""){
                        count = 0;
                    }else{
                        count = parseInt(count);
                    }
                    $("#plcount_"+object_id).text(count+1);
                    div.fadeOut();
                }
            });
        });
        btn_cancel.click(function(){
            div.fadeOut();
        });
    };
})(jQuery);