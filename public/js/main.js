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