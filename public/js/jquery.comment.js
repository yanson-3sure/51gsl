(function ($) {
    $.fn.comment = function(options){
        var defaults = {
            comment_div:"#plk",
            comment_form:"#comment_form",
            comment_post : ".plk_pl",
            comment_cancel:'.plk_qx'
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
            div.show();
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
                    var content = '<p class="reply_comment" data-object-id="'+object_id+'" data-reply-id="'+data.comment_id+'">';
                    content += "<a href='javascript:;'>"+name + '</a>:';
                    if(reply_nickname!=''){
                        content += '<span>回复</span> ' + "<a href='javascript:;'>" + reply_nickname + "</a>:";
                    }
                    content += "<span>"+data.comment+"</span>";
                    content +='</p>';
                    //console.log(content);
                    $('#pinglun_'+object_id).append(content);
                    var count = $('#plcount_'+object_id).text();
                    if(count==""){
                        count = 0;
                    }else{
                        count = parseInt(count);
                    }
                    $("#plcount_"+object_id).text(count+1);
                    div.hide();
                }
            });
        });
        btn_cancel.click(function(){
            div.hide();
        });
    };
})(jQuery);