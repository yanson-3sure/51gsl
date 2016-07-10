$(document).ready(function(){

	//, textStatus, errorThrown
	var ajaxError = function(XMLHttpRequest,callback422) {
		//if (XMLHttpRequest.status == 0) {
		if(false){
			layer.closeAll();
			layer.msg('请检查你的网络');
		} else if (XMLHttpRequest.status == 401) {
			layer.closeAll();
			layer.msg('登录超时,请重新登录',function(){location.href='/auth/login';});
		} else if (XMLHttpRequest.status == 403) {
			layer.closeAll();
			layer.msg('您没有权限',function(){location.href='/';});
		}else if (XMLHttpRequest.status == 422) {
			if(callback422){
				layer.closeAll();
				callback422(JSON.parse(XMLHttpRequest.responseText));
				return;
			}
			data = JSON.parse(XMLHttpRequest.responseText);
			errorMsg = '';
			$.each(data,function(name,value) {
				errorMsg += value + '<br>';
			});
			layer.closeAll();
			layer.msg(errorMsg);
		} else if (XMLHttpRequest.status == 500) {
			layer.closeAll();
			layer.msg('服务器错误');
		} else if (XMLHttpRequest.status == 501) {
			layer.closeAll();
			layer.msg(XMLHttpRequest.responseText);
		}  else {
			//layer.closeAll();
			//layer.msg('未知错误.\n' + XMLHttpRequest.responseText);
		}
	}
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
	$(document).ajaxError(function( event, jqxhr, settings, thrownError ){
		ajaxError(jqxhr);
	});
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
				$('.weui_tabbar a').first().find('.point').show();
				$('#home_noreadcount').text(data.count + '条未读').show();
			}else {
				$('.weui_tabbar a').first().find('.point').hide();
				$('.news-tip').hide();
			}
		}
	});
	setTimeout(getNoreadcount,1000*10);
}
var weDialog = {
	msg: function(content, options, end){
		return layer.msg(content, options, end);
	},
	show:function(img,id,callback){
		if($('#'+id).length==0){
			$('body').append('<image src="'+img+'" id="'+id+'" style="display: none">');
		}
		var h = document.body.scroolTop;
		$('#'+id).css({"top": h + 50 + "px"})
				.fadeIn("fast")
				.delay(1000)
				.fadeOut("fast", callback);
	},
	deleteOk:function(callback){
		var img = '/img/yishanchu.svg';
		var id = 'deleteSuccess';
		this.show(img,id,callback);
	},
	saveOk:function(callback){
		var img = '/img/yibaocun.svg';
		var id = 'saveSuccess';
		this.show(img,id,callback);
	},
	sendOk:function(callback){
		var img = '/img/yifasong.svg';
		var id = 'sendSuccess';
		this.show(img,id,callback);
	}
};
var getUrlParameter = function getUrlParameter(sParam) {
	var sPageURL = decodeURIComponent(window.location.search.substring(1)),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

	for (i = 0; i < sURLVariables.length; i++) {
		sParameterName = sURLVariables[i].split('=');

		if (sParameterName[0] === sParam) {
			return sParameterName[1] === undefined ? true : sParameterName[1];
		}
	}
};