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
var getNoreadcount = function(){
    $.ajax({
        type: "get",
        url: '/my/message/noreadcount',
        dataType: 'json',
        headers: {  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  },
        success: function(data){
            if(data.count && data.count>0){
                $('#numTip').text(data.count);
                $('#numTip').attr('class',(data.count+'').length==1?'fot-zb-1 fot':'fot-zb-2 fot');
                $('#numTip').show();
                $('#home_noreadcount').text(data.count + '条未读');
                $('#home_noreadcount').show();
            }else if(data.count && data.count==0){
                $('#numTip').hidden();
                $('#home_noreadcount').hidden();
            }
        }
    });
    setTimeout(getNoreadcount,1000*10);
}
function ScrollPagination(obj, url, type, heightOffset, loading)
{
    $(obj).scrollPagination({
        'getResource': function (index) {
            var html = '';
            $.ajax({
                type: type ? type :"GET",
                url: url,
                data: { "page": index + 1 },
                async: false,
                cache: false,
                dataType: 'html',
                success: function (data) {
                    html = data;
                },
                error: function () {
                }
            });
            return html;
        },
        'scrollTarget': $(window), // who gonna scroll? in this example, the full window
        'heightOffset': heightOffset ? heightOffset: 200, // it gonna request when scroll is 10 pixels before the page ends
        'beforeLoad': function () { // before load function, you can display a preloader div
            if (!loading) { loading = "loadingtab"; }
            if ($("#" + loading).length > 0)
            {
                $("#" + loading).show();
            }
        },
        'afterLoad': function (elementsLoaded) { // after loading content, you can use this function to animate your
            setTimeout(function () { celLoading(loading); }, 3000);
        }
    });
}
$(document).ready(function(){
    $("#thelist li").hover(function(){
        $(this).css("background-color","#f7f7f9").siblings().css("background","#ffffff");
    });	//hover改变背景颜色

});
var smsTiming = function(fasongClass){
    var tleft = $(fasongClass).attr('tleft');
    //console.log(tleft);
    if(tleft>0){
        $(fasongClass).attr('tleft',tleft-1);
        $(fasongClass).text(tleft+'s后重新发送');
        setTimeout(function(){smsTiming(fasongClass);},1000);
        return;
    }
    $(fasongClass).attr('sending',"0");
    $(fasongClass).text('获取验证码');
}
var sendSms = function (fasongClass) {
    $(fasongClass).click(function(){
        if($(fasongClass).attr('sending')=="1"){
            return false;
        }
        $(fasongClass).attr('sending',"1");
        $.ajax({
            url: "/sms",
            type: 'post',
            data: {'mobile':$('#mobile').val()},
            success: function(data) {
                if(data.result=="success"){
                    $(fasongClass).attr('tleft',60);
                    smsTiming(fasongClass);
                }else{
                    layer.msg(data.msg);
                    $(fasongClass).attr('sending',"0");
                }
            },
            error:  function(XMLHttpRequest) {
                ajaxError(XMLHttpRequest);
                $('#mobile').focus();
                $(fasongClass).attr('sending',"0");
            }
        });
    });
}

var focus1 = function(obj,focusclass,unfocusclass){
    var url = '/my/follow/focus';
    if($(obj).attr('data-type')=='1'){//已经 关注
        url = '/my/follow/un-focus';
    }
    $.ajax({
        type: "post",
        url: url,
        dataType: 'json',
        headers: {  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  },
        data: {"fuserid":$(obj).attr('data-fuserid')},
        success: function(data){
            if(data.error) {
                layer.msg('ERROR:'+data.error);
                return false;
            }
            if(typeof(focusclass) === 'boolean' && !focusclass){
                if($(obj).attr('data-type')=='1'){//已经 关注
                    $(obj).attr('data-type','0');
                    $(obj).text("关注");
                } else {
                    $(obj).attr('data-type','1');
                    $(obj).text("已关注");
                }
                return;
            }
            if(!focusclass) focusclass = 'ms-r-1-r';
            if(!unfocusclass) unfocusclass = 'ms-r-1-r2';

            if($(obj).attr('data-type')=='1'){//已经 关注
                $(obj).removeClass(focusclass);
                $(obj).addClass(unfocusclass);
                $(obj).attr('data-type','0');
                $(obj).text("关注");
            } else {
                $(obj).removeClass(unfocusclass);
                $(obj).addClass(focusclass);
                $(obj).attr('data-type','1');
                $(obj).text("已关注");
            }
        }
    });
}
var fancybox = function(className){
    if(!className)  className = '.fancybox-effects';
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