$(document).ready(function(){

	//, textStatus, errorThrown
	var ajaxError = function(XMLHttpRequest,callback422) {
		if (XMLHttpRequest.status == 0) {
			layer.msg('请检查你的网络');
		} else if (XMLHttpRequest.status == 401) {
			layer.msg('登录超时,请重新登录',function(){location.href='/auth/login';});
		} else if (XMLHttpRequest.status == 403) {
			layer.msg('您没有权限',function(){location.href='/';});
		}else if (XMLHttpRequest.status == 422) {
			if(callback422){
				callback422(JSON.parse(XMLHttpRequest.responseText));
				return;
			}
			data = JSON.parse(XMLHttpRequest.responseText);
			errorMsg = '';
			$.each(data,function(name,value) {
				errorMsg += value + '<br>';
			});
			layer.msg(errorMsg);
		} else if (XMLHttpRequest.status == 500) {
			layer.msg('服务器错误');
		} else if (XMLHttpRequest.status == 501) {
			layer.msg(XMLHttpRequest.responseText);
		}  else {
			layer.msg('未知错误.\n' + XMLHttpRequest.responseText);
		}
	}
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
	$(document).ajaxError(function( event, jqxhr, settings, thrownError ){
		ajaxError(jqxhr);
	});
	//// 顶部tab选中切换及标签页切换
	//$("div.weui_navbar_item").click(function(){
	//	if($(this).attr("name") == "subnav"){
	//		$(this).addClass("weui_bar_item_on_other").siblings().removeClass("weui_bar_item_on_other");
	//		var name = $(this).attr("role");
	//		$(".tab-content").each(function(){
	//			if($(this).attr("name") == name){
	//				$(this).css("display","block").siblings().css("display","none");
	//			}
	//		});
	//	}else{
	//		$(this).addClass("weui_bar_item_on").siblings().removeClass("weui_bar_item_on");
	//		var name = $(this).attr("role");
	//		$(".tab-content").each(function(){
	//			if($(this).attr("name") == name){
	//				$(this).css("display","block").siblings().css("display","none");
	//			}
	//		});
	//	}
	//});
	// 回顶按钮
	$(window).scroll(function(){
		var h = $(window).scrollTop();
		// 下滑距离超过600px，显示回顶按钮
		if (h > 600){
			$(".back2top").show();
			$(".back2top").click(function(){
				$(window).scrollTop(0);
			});
		}else{
			$(".back2top").hide();
		}
	});

}); // ready end
var fancybox = function(className){
	if(!className)  className = '.thumbnail';
	$(className).fancybox({
		wrapCSS :'fancybox-custom',
		closeClick: true,
		openEffect:'none',
		helpers:{
			title:{
				type:'inside'
			},
			overlay:{
				css:{'background':'rgba(0,0,0,0.5)'}
			}
		}
	});
}
var getNoreadcount = function(){
	$.ajax({
		type: "get",
		url: '/my/message/noreadcount',
		dataType: 'json',
		success: function(data){
			if(data.count && data.count>0){
				if($('.weui_tabbar a').first().find('.point').length==0){
					$('.weui_tabbar a').first().append('<div class="point"></div>');
				}
				$('.news-tip').show();
				$('#home_noreadcount').text(data.count + '条未读').show();
			}else if(data.count && data.count==0){
				$('.weui_tabbar a').first().find('.point').hide();
				$('.news-tip').hide();
			}
		}
	});
	setTimeout(getNoreadcount,1000*10);
}
function deleteStatus(obj){
	// 点击消息框三个圆点图标，弹出底部菜单
	$(obj).unbind("click").click(function(){
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

		// 点击删除
		$("#deleteMsg").unbind("click").bind("click",function(){
			$.ajax({
				type: "DELETE",
				url: '/status/'+that.parents(".msg-container").attr('data-id'),
				data: {},
				success: function (data) {
					var h = document.body.scroolTop;
					hideActionSheet(weuiActionsheet, mask);
					$("#deleteSuccess").css({"top":h + 50 + "px"})
							.fadeIn("fast")
							.delay(1000)
							.fadeOut("fast",function(){
								that.parents(".msg-container").slideUp("slow",function(){
									$(this).remove();
								});
							});
				},
			});

		});

	});
}
/*
 * 创建main的目的是当下拉或上拉加载内容后，新的内容需要再次绑定相关方法
 */

function main(){};

main.prototype = {
	fresh : function(){
		// 点击消息框三个圆点图标，弹出底部菜单
		//$(".handle").unbind("click").click(function(){
		//	var that = $(this);
		//	var mask = $('#mask');
		//	var weuiActionsheet = $('#weui_actionsheet');
		//	weuiActionsheet.addClass('weui_actionsheet_toggle');
		//	mask.show()
		//			.focus()
		//			.addClass('weui_fade_toggle').one('click', function () {
		//		hideActionSheet(weuiActionsheet, mask);
		//	});
		//	$('#actionsheet_cancel').one('click', function () {
		//		hideActionSheet(weuiActionsheet, mask);
		//	});
		//	mask.unbind('transitionend').unbind('webkitTransitionEnd');
        //
		//	function hideActionSheet(weuiActionsheet, mask) {
		//		weuiActionsheet.removeClass('weui_actionsheet_toggle');
		//		mask.removeClass('weui_fade_toggle');
		//		mask.on('transitionend', function () {
		//			mask.hide();
		//		}).on('webkitTransitionEnd', function () {
		//			mask.hide();
		//		})
		//	}
        //
		//	// 点击删除
		//	$("#deleteMsg").unbind("click").bind("click",function(){
		//		var h = document.body.scroolTop;
		//		hideActionSheet(weuiActionsheet, mask);
		//		$("#deleteSuccess").css({"top":h + 50 + "px"})
		//				.fadeIn("fast")
		//				.delay(1000)
		//				.fadeOut("fast",function(){
		//					that.parents(".msg-container").slideUp("slow",function(){
		//						$(this).remove();
		//					});
		//				});
		//	});

		//});

		// 点赞
		//$(".appreciate").unbind("click").on("click",function(){
		//	var status = $(this).attr("status");
		//	if(status == 0){
		//		$(this).attr("status",1).find("img").css("opacity",0);
		//		var num = $(this).find("strong");
		//		num.text(parseInt(num.text()) + 1);
		//	}else{
		//		$(this).attr("status",0).find("img").css("opacity",1);
		//		var num = $(this).find("strong");
		//		num.text(parseInt(num.text()) - 1);
		//	}
		//});

		// 发表评论
		//$(".make-comment").unbind("click").click(function(){
		//	var number = $(this).find("strong");
		//	var num = parseInt(number.text());
		//	var name = $(this).parents(".msg-container").find(".pub-name a").text();
        //
		//	// 显示评论框
		//	$("div.mask").fadeIn().find(".comment-box").css("top",0).animate({"top":"50px"}).find("textarea")
		//			.val('').attr("placeholder","回复：" + name).focus();
        //
		//	// 取消按钮
		//	$("div.mask .cancel").click(function(){
		//		$("div.mask").fadeOut();
		//	});
        //
		//	// 发送按钮
		//	$("div.mask .send").unbind("click").bind("click" ,function(){
		//		var success = $("#sendSuccess");
		//		var text = $("div.mask textarea");
		//		var content = text.val();
		//		if(content.length == 0){
		//			text.focus();
		//			return;
		//		}else{
		//			$("div.mask").fadeOut("normal",function(){
		//				var h = document.body.scrollTop;
		//				// 发送成功提示
		//				success.css({"top":h + 50 + "px"})
		//						.fadeIn("fast")
		//						.delay("1000")
		//						.fadeOut("normal",function(){
		//							// 评论数+1
		//							number.text(++num);
		//						});
		//			});
		//		}
		//	});
		//});

		// 回复他人的评论
		//$(".comment-content p").unbind("click").click(function(){
		//	var success = $("#sendSuccess");
		//	var status = $(this).attr("status");
		//	var name = $(this).find("span").first().text();
		//	// 切掉冒号
		//	if($(this).attr("status") == 0){
		//		name = name.slice(0,name.length - 1);
		//	};
		//	// 显示评论框
		//	$("div.mask").fadeIn().find(".comment-box").css("top",0).animate({"top":"50px"}).find("textarea")
		//			.val('').attr("placeholder","回复：" + name).focus();
        //
		//	// 取消按钮
		//	$("div.mask .cancel").click(function(){
		//		$("div.mask").fadeOut();
		//	});
        //
		//	// 发送按钮
		//	$("div.mask .send").unbind("click").bind("click" ,function(){
		//		var text = $("div.mask textarea");
		//		var content = text.val();
		//		if(content.length == 0){
		//			text.focus();
		//			return;
		//		}else{
		//			$("div.mask").fadeOut("normal",function(){
		//				var h = document.body.scrollTop;
		//				// 发送成功提示
		//				success.css({"top":h + 50 + "px"}).fadeIn("fast").delay("1000").fadeOut("normal")
		//			});
		//		}
		//	});
		//})

		// 关注讲师
		//$(".guanzhu").unbind("click").click(function(){
		//	if($(this).attr("name") == "other"){
		//		return
		//	}else{
		//		var that = $(this);
		//		var status = that.attr("status");
		//		if(status == 0){
		//			that.attr("status",1).text("已关注").css({
		//				"color":"#999","border-color":"#eee","background-color":"#eee"
		//			});
		//		}else{
		//			that.text("关注").css({
		//				"color":"#ff4444", "border-color":"#ff4444", "background-color":"transparent"
		//			});
		//			that.attr("status",0);
		//		}
		//	}
		//});

		// 点击缩略图展开大图
		//$(".thumbnail").unbind("click").click(function(){
		//	var $layer = $("<div class='backlayer'><div/>").css({
		//		position: "fixed",
		//		left: 0,
		//		right: 0,
		//		top: 0,
		//		bottom: 0,
		//		height: "100%",
		//		width: "100%",
		//		zIndex: 9999999,
		//		background: "rgba(0,0,0,.5)"
		//	});
		//	var addr = $(this).find("img").attr("src");
		//	var img = $("<img src="+ addr +" />");
		//	$layer.appendTo("body");
		//	img.appendTo(".backlayer");
		//	$(".backlayer img").css({
		//		display:"block",
		//		width:"100%",
		//		position:"absolute",
		//		top:0,
		//		bottom:0,
		//		margin:"auto"
		//	});
        //
		//	$(".backlayer").click(function(){
		//		$(this).remove();
		//	});
		//});

		//// 问答模块，点击更多展开内容
		//$(".queswrap .answer").unbind("click").on("click",function(){
		//	$(this).css("display","block");
		//});
        //
		//// 点击免费试用，弹出提示框
		//$("button.freeuse,button.free").unbind("click").click(function(){
		//	var dia = $("#dialog2");
		//	dia.fadeIn();
        //
		//	$(".weui_mask").click(function(){
		//		dia.fadeOut();
		//	});
        //
		//	$("a.close").click(function(){
		//		dia.fadeOut();
		//	});
		//});
	}
}

//var main = new main();
//main.fresh();