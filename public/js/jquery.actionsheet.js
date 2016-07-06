(function ($) {
    $.fn.actionsheet = function(options){
        var defaults = {
            op:'status'
        }
        var options = $.extend(defaults, options);
        this.each(function(){
            var _this = $(this);
            if($._data(_this, 'events' )){
                return ;
            };
            // 点击消息框三个圆点图标，弹出底部菜单
            _this.click(function() {
                var that = $(this);
                var mask = $('#mask');
                var weuiActionsheet = $('#weui_actionsheet');
                weuiActionsheet.addClass('weui_actionsheet_toggle');
                mask.show()
                    .focus()
                    .addClass('weui_fade_toggle').one('click', function () {
                    hideActionSheet(weuiActionsheet, mask);
                });
                $('#actionsheet_cancel').one('click', function () {
                    hideActionSheet(weuiActionsheet, mask);
                });
                mask.unbind('transitionend').unbind('webkitTransitionEnd');

                function hideActionSheet(weuiActionsheet, mask) {
                    weuiActionsheet.removeClass('weui_actionsheet_toggle');
                    mask.removeClass('weui_fade_toggle');
                    mask.on('transitionend', function () {
                        mask.hide();
                    }).on('webkitTransitionEnd', function () {
                        mask.hide();
                    })
                }
                if(options.op=='status') {
                    // 点击删除
                    $("#deleteMsg").unbind("click").bind("click", function () {
                        $.ajax({
                            type: "DELETE",
                            url: '/status/' + that.parents(".msg-container").attr('data-id'),
                            data: {},
                            success: function (data) {
                                var h = document.body.scroolTop;
                                hideActionSheet(weuiActionsheet, mask);
                                $("#deleteSuccess").css({"top": h + 50 + "px"})
                                    .fadeIn("fast")
                                    .delay(1000)
                                    .fadeOut("fast", function () {
                                        that.parents(".msg-container").slideUp("slow", function () {
                                            $(this).remove();
                                        });
                                    });
                            }
                        });

                    });
                }
                if(options.op=='strategy'){

                }
            });
        });
    };
})(jQuery);