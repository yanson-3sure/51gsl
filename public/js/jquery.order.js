(function ($) {
    $.fn.order = function(options){
        var defaults = {
            callback:function(){},
            url:'/my/order/'
        }
        var options = $.extend(defaults, options);
        this.each(function(){
            $(this).click(function(){
                var _this = $(this);
                var type = $(this).attr('data-type');
                var product_id = $(this).attr('data-product-id');
                var product_type = $(this).attr('data-product-type');
                var month = $(this).attr('data-month');
                if(product_id<1){
                    layer.msg('产品不能为空');
                    return false;
                }
                $.post(options.url,{type:type,product_id:product_id,product_type:product_type,month:month}, function (data) {
                    var dia = $("#dialog2");
                    dia.fadeIn();

                    $(".weui_mask").click(function(){
                        dia.fadeOut();
                    });

                    $("a.close").click(function(){
                        dia.fadeOut();
                    });
                    if(options.callback){
                        options.callback(data);
                    }
                });
            });
        });
    };
})(jQuery);