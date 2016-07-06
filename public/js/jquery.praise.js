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
                var _this = $(this);
                var object_id = _this.attr('data-object-id');
                var object_type = _this.attr('data-object-type');
                var uid = options.uid;
                var num = _this.find("strong");
                var count = num.text();
                if (count == "") {
                    count = 0;
                } else {
                    count = parseInt(count);
                }
                var data = {"object_id": object_id, "object_type": object_type};
                var status = _this.attr("status");
                var list = _this.parents('.footer').find('.reviewers');
                if(status == 0){
                    $.ajax({
                        type: "post",
                        url: '/my/praise',
                        dataType: 'json',
                        data: data,
                        success: function (data) {
                            //console.log(data);
                            count++;
                            if(list.length>0) {
                                if (count == 1) {
                                    list.show();
                                }
                                list.append('<img src="' + data.user.avatar + '">');
                            }
                            num.text(count);//prepend
                            _this.attr("status",1).find("img").css("opacity",0);
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
                            if(list.length>0) {
                                if (count == 0) {
                                    list.hide()
                                }
                                list.find('img[src="' + data.user.avatar + '"]').remove();
                            }
                            _this.attr("status",0).find("img").css("opacity",1);
                        },
                    });
                }
            });
        });
    };
})(jQuery);