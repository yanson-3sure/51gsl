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
        url: '/my/noreadcount',
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
var praise =function(statusid){
    var userid = $('#userid').val();
    var a = $('#btn_praise_'+statusid);
    var span = a.find('span').first();
    var span2 = a.find('span').first().find('span');
    var count = span2.text();
    if(count==""){
        count = 0;
    }else{
        count = parseInt(count);
    }
    //console.log(a.html());
    //console.log(count);
    //console.log(span.attr('class'));
    if(span.attr('class')==""){
        $.ajax({
            type: "post",
            url: '/praise',
            dataType: 'json',
            data: {"statusid":statusid},
            success: function(data){
                if(data.result.error) {
                    layer.msg(data.result.error);
                    return false;
                }
                //console.log(data);
                span.attr('class','red');
                $('#icon_p_'+statusid).attr('class','icon-heart');
                count++;
                if(count==1){
                    $('#zan-l-'+statusid).show();
                }
                span2.text(count);//prepend
                $('#zan-r_'+statusid).append("<a href='javascript:;'><div  class='zan-r-a' id='praise_"+statusid + "_"+ userid+"'><img src='"+$("#avatar").val()+"' width='28px' height='28px'></div></a>");
            }
        });
    }else{
        $.ajax({
            type: "DELETE",
            url: '/praise/0',
            data: {"statusid":statusid},
            success: function(data){
                if(data.result.error) {
                    layer.msg('ERROR:'+data.result.error);
                    return false;
                }
                //console.log(data);
                span.attr('class','');
                count--;
                if(count==0){
                    span2.text('');
                }else {
                    span2.text(count);
                }
                if(count==0){
                    $('#zan-l-'+statusid).hide();
                }
                $('#icon_p_'+statusid).attr('class','icon-heart-empty');
                //console.log("praise_"+statusid + "_"+ userid);
                $("#praise_"+statusid + "_"+ userid).remove();
            },
        });
    }
}
var showcomment = function(statusid,reply_commentid,replay_nickname){
    if($('#plk').length==0){layer.msg('请先登录',function(){location.href='/auth/login'});}
    //var userid = $('#userid').val();
    $('#comment_statusid').val(statusid);
    $('#reply_commentid').val(reply_commentid);
    $('#comment_body').val('');
    $('#r_comment').attr("checked", false);
    if(replay_nickname==""){
        $('#comment_body').attr('placeholder','');
    }else{
        $('#comment_body').attr('placeholder','回复'+replay_nickname);
    }
    //$("#plk").css({bottom:0, left:0 });//设置弹出层位置
    //	$("#plk").show().focus();//动画显示
    //	$(".textarea-control").trigger("select");
    $("#plk").css({bottom:0, left:0 });//设置弹出层位置
    $('#plk').show().focus();
    $(".textarea-control").trigger("select");
}
var hideComment = function(){
    $('#plk').hide();
}
var comment = function(){
    if($('#comment_body').val()==""){layer.msg('评论内容不能为空');return;}
    $.ajax({
        type: "post",
        url: '/comment',
        dataType: 'json',
        data: $("#comment_form").serializeArray(),
        success: function(data){
            //console.log(data);
            if(data.error) {
                layer.msg(data.error);
                hideComment();
                return false;
            }
            var statusid = $('#comment_statusid').val();
            var nickname = $('#nickname').val() ;
            var reply_nickname = data.nickname ? data.nickname : '';
            var content = '<p onclick="showcomment(\''+statusid+'\',\''+data.commentid+'\',\''+nickname+'\')" >';
            content += "<a href='javascript:;'>"+nickname + '</a>:';
            if(reply_nickname!=''){
                content += '<span>回复</span> ' + "<a href='javascript:;'>" + reply_nickname + "</a>:";
            }
            content += "<span>"+data.body+"</span>";
            content +='</p>';
            //console.log(content);
            $('#pinglun_'+statusid).append(content);
            var count = $('#plcount_'+statusid).text();
            if(count==""){
                count = 0;
            }else{
                count = parseInt(count);
            }
            $('#plcount_'+statusid).text(count+1);
            hideComment();
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
function setImagePreviews(avalue) {
    var docObj = document.getElementById("doc");
    var dd = document.getElementById("crtp");
    dd.innerHTML = "";
    var fileList = docObj.files;
    for (var i = 0; i < fileList.length; i++) {
        dd.innerHTML += "<div style='float:left' > <img id='img" + i + "'  /> </div>";
        var imgObjPreview = document.getElementById("img"+i);
        if (docObj.files && docObj.files[i]) {
            //火狐下，直接设img属性
            imgObjPreview.style.display = 'block';
            imgObjPreview.style.width = '100px';
            imgObjPreview.style.height = '120px';
            //imgObjPreview.src = docObj.files[0].getAsDataURL();
            //火狐7以上版本不能用上面的getAsDataURL()方式获取，需要一下方式
            imgObjPreview.src = window.URL.createObjectURL(docObj.files[i]);
        }
        else {
            //IE下，使用滤镜
            docObj.select();
            var imgSrc = document.selection.createRange().text;
            //alert(imgSrc)
            var localImagId = document.getElementById("img" + i);
            //必须设置初始大小
            localImagId.style.width = "100px";
            localImagId.style.height = "120px";
            //图片异常的捕捉，防止用户修改后缀来伪造图片
            try {
                localImagId.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale)";
                localImagId.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
            }
            catch (e) {
                alert("您上传的图片格式不正确，请重新选择!");
                return false;
            }
            imgObjPreview.style.display = 'none';
            document.selection.empty();
        }
    }
    return true;
};