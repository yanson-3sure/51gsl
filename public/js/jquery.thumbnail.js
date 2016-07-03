(function ($) {
    $.fn.thumbnail = function(options){
        var defaults = {
        }
        var options = $.extend(defaults, options);
        this.each(function(){
            var _this = $(this);
            if($._data(_this, 'events' )){
                return ;
            };
            $(this).click(function(){
                var $layer = $("<div class='backlayer'><div/>").css({
                    position: "fixed",
                    left: 0,
                    right: 0,
                    top: 0,
                    bottom: 0,
                    height: "100%",
                    width: "100%",
                    zIndex: 9999999,
                    background: "rgba(0,0,0,.5)"
                });
                var addr = $(this).find("img").attr("data-big-src");
                var img = $("<img src="+ addr +" />");
                $layer.appendTo("body");
                img.appendTo(".backlayer");
                $(".backlayer img").css({
                    display:"block",
                    width:"100%",
                    position:"absolute",
                    top:0,
                    bottom:0,
                    margin:"auto"
                });

                $(".backlayer").click(function(){
                    $(this).remove();
                });
            });
        });
    };
})(jQuery);