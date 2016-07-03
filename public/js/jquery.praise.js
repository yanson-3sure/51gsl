(function ($) {
    $.fn.praise = function(options){
        var defaults = {
            uid:"0",
            avatar:''
        }
        var options = $.extend(defaults, options);
        this.each(function(){
            var _this = $(this);
            if($._data(_this, 'events' )){
                return ;
            };
            var status = $(this).attr("status");
            if(status == 0){
                $(this).find("img").css("opacity",1);
            }else{
                $(this).find("img").css("opacity",0);
            }
            $(this).click(function(){
                var obj = $(this);
                var object_id = $(this).attr('data-object-id');
                var object_type = $(this).attr('data-object-type');
                var uid = options.uid;
                var num = $(this).find("strong");
                var count = num.text();
                if (count == "") {
                    count = 0;
                } else {
                    count = parseInt(count);
                }
                var data = {"object_id": object_id, "object_type": object_type};
                var status = $(this).attr("status");
                if(status == 0){
                    $.ajax({
                        type: "post",
                        url: '/my/praise',
                        dataType: 'json',
                        data: data,
                        success: function (data) {
                            //console.log(data);
                            count++;
                            if (count == 1) {
                                //$('#zan-l-' + object_id).show();
                            }
                            num.text(count);//prepend
                            obj.attr("status",1).find("img").css("opacity",0);
                        }
                    });
                }else{
                    $.ajax({
                        type: "DELETE",
                        url: '/my/praise/0',
                        data: data,
                        success: function (data) {
                            count--;
                            if (count == 0) {
                                num.text(count);
                            } else {
                                num.text(count);
                            }
                            if (count == 0) {
                                //$('#zan-l-' + object_id).hide();
                            }
                            //$('#icon_p_' + object_id).attr('class', 'icon-heart-empty');
                            ////console.log("praise_"+object_id + "_"+ userid);
                            //$("#praise_" + object_id + "_" + uid).remove();
                            obj.attr("status",0).find("img").css("opacity",1);
                        },
                    });
                }
            });
        });
    };
})(jQuery);