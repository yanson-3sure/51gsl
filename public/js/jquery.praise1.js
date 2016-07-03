(function ($) {
    $.fn.praise = function(options){
        var defaults = {
            uid:"0",
            avatar:''
        }
        var options = $.extend(defaults, options);
        this.each(function(){
            $(this).click(function(){
                var object_id = $(this).attr('data-object-id');
                var object_type = $(this).attr('data-object-type');
                var uid = options.uid;
                var span = $(this).find('span').first();
                var span2 = $(this).find('span').first().find('span');
                var count = span2.text();
                if (count == "") {
                    count = 0;
                } else {
                    count = parseInt(count);
                }
                var data = {"object_id": object_id, "object_type": object_type};
                if (!span.attr('class')) {
                    $.ajax({
                        type: "post",
                        url: '/my/praise',
                        dataType: 'json',
                        data: data,
                        success: function (data) {
                            //console.log(data);
                            span.attr('class', 'red');
                            $('#icon_p_' + object_id).attr('class', 'icon-heart');
                            count++;
                            if (count == 1) {
                                $('#zan-l-' + object_id).show();
                            }
                            span2.text(count);//prepend
                            $('#zan-r_' + object_id).append("<a href='javascript:;'><div  class='zan-r-a' id='praise_" + object_id + "_" + uid + "'><img src='" + options.avatar + "' width='28px' height='28px'></div></a>");
                        }
                    });
                } else {
                    $.ajax({
                        type: "DELETE",
                        url: '/my/praise/0',
                        data: data,
                        success: function (data) {
                            span.attr('class', '');
                            count--;
                            if (count == 0) {
                                span2.text('');
                            } else {
                                span2.text(count);
                            }
                            if (count == 0) {
                                $('#zan-l-' + object_id).hide();
                            }
                            $('#icon_p_' + object_id).attr('class', 'icon-heart-empty');
                            //console.log("praise_"+object_id + "_"+ userid);
                            $("#praise_" + object_id + "_" + uid).remove();
                        },
                    });
                }
            });
        });
    };
})(jQuery);