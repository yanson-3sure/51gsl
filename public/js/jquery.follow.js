(function ($) {
    $.fn.follow = function(options){
        var defaults = {
            no_css : {"color":"#ff4444", "border-color":"#ff4444", "background-color":"transparent"},
            ok_css : {"color":"#999","border-color":"#eee","background-color":"#eee"}
        }
        var options = $.extend(defaults, options);
        this.each(function(){
            var _this = $(this);
            if($._data(_this, 'events' )){
                return ;
            };
            var status = $(this).attr("status");
            if(status == 0){
                _this.text("未关注").css(options.no_css);
            }else{
                _this.text("已关注").css(options.ok_css);
            }
            $(this).click(function(){
                var _this = $(this);
                var status = _this.attr("status");
                var url = '/my/follow/focus';
                if(status==1){//已经 关注
                    url = '/my/follow/un-focus';
                }

                $.ajax({
                    type: "post",
                    url: url,
                    dataType: 'json',
                    headers: {  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  },
                    data: {"fuserid":_this.attr('data-fuserid')},
                    success: function(data){
                        if(data.error) {
                            layer.msg('ERROR:'+data.error);
                            return false;
                        }
                        if(status == 0){
                            _this.attr("status",1).text("已关注").css(options.ok_css);
                        }else{
                            _this.attr("status",0).text("未关注").css(options.no_css);
                        }
                    }
                });
            });
        });
    };
})(jQuery);