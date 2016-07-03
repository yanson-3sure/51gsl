(function ($) {
    $.fn.loadmore = function(options){
        var defaults = {
            callback:function(){}
        }
        var options = $.extend(defaults, options);
        this.each(function(){
            $(this).click(function(){
                var _this = $(this);
                var url = $(this).attr('data-url');
                var append_object = $(this).attr('data-append-object');
                var max = $(this).attr('data-max');
                var isMore = $(this).attr('data-is-more');
                if(isMore=='0') return;
                $.get(url,{max:max}, function (data) {
                    $(append_object).append(data.content);
                    _this.attr('data-max',data.max);
                    if(!data.isMore){
                        _this.hide();
                        _this.attr('data-is-more','0');
                        //layer.msg('没有更多信息');
                    }
                    if(options.callback){
                        options.callback(data);
                    }
                });
            });
        });
    };
})(jQuery);