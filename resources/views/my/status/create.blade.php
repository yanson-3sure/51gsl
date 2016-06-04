@extends('layouts.master_nofooter')
@section('title', '创建文章内容')
@section('content')
    <form id="myForm" method="post" action="/my/status">
        <div class="fbxx" id="fbxx" data-url="/status-upload?type=status">
            <div class="fbxxk">
                <div class="fbxx-top">
                    <div class="fbxx_qx"><a href="/my">取消</a></div>
                    <div class="fbxx_pl">发表</div>
                </div>
                @if(isset(config('base.dyxc.users')[$uid]))
                    <p>
                        <input type='radio' name='NewsType' value='1' checked/>大盘
                        <input type='radio' name='NewsType' value='2'/>名家论市
                        <input type='radio' name='NewsType' value='3'/>热股
                        <input type='radio' name='NewsType' value='4'/>消息
                        <input type='radio' name='NewsType' value='5'/>独家
                        <input type='radio' name='NewsType' value='6'/>个股关注
                        <input type='radio' name='NewsType' value='7'/>绩效回顾
                    </p>
                @endif
                <p class="emoji-picker-container">
                    <textarea name="message" id="message" class="form-control textarea-control" rows="3"></textarea>
                </p>
                <!-- 加载编辑器的容器 -->

                <div class="charu">
                    <input type="file" name="file" id="doc"  accept="image/*">
                    <input type="hidden" name="image_id" id="image_id" value="0" >
                    <i class="icon-picture"></i>
                </div>
            </div>
            <div class="crtp" id="crtp" style="display:none">
                <a href="#" class="thumbnail">
                    <img src="" class="img-responsive">
                </a>
            </div>
            <input style="display: none;" type="text" class="form-control input-block" name="file_path" value="" readonly>
        </div>
    </form>
@endsection
@section('footer')
    @parent
    <script src="/js/jquery.ui.widget.js"></script>
    <script src="/js/jquery.fileupload.js"></script>
    <link href="/css/jquery.fancybox.css" rel="stylesheet" type="text/css">
    <script src="/js/jquery.fancybox.pack.js"></script>
    <script>
        $(function(){
            fancybox('.thumbnail');

            $('.fbxx_pl').click(function(){
                if($('#message')==""){
                    layer.msg("发表内容不能为空");
                    return false;
                }
                layer.msg('发送中...');
                $('#myForm').ajaxSubmit({
                    success: function(data) {
                        layer.msg(data.result,function(){location.href="/my";});
                    }
                });
            });
            //http://laravel-media-upload.triasrahman.com/
            var buttonUpload = '.charu';
            var settings = {
                autoUpload : true,
                add : function (e, data) {
                    data.submit();
                },
                submit : function (e, data) {
                    $(this).find(buttonUpload).attr('disabled', true);

                },
                done : function (e, data) {
                    if(data.result.error) {
                        layer.msg('ERROR:'+data.result.error);
                        return false;
                    }
                    if(data.result.format == 'image') {

                        $(this).find('.thumbnail').show().find('img').attr('src','/'+data.result.path.replace(data.result.name,data.result.name+'-'+88));
                        $(this).find('.thumbnail').attr('href','/'+data.result.path);
                        $(this).find('.crtp').show();
                    }
                    else {
                        $(this).find('.thumbnail').hide()
                        $(this).find('.crtp').hide();
                    }

                    $(this).find('input[name=file_path]').val(data.result.path);
                    $(this).find('#image_id').val(data.result.image_id);
                    layer.msg('上传成功');
                },

                stop : function (e) {
                    $(this).find(buttonUpload).removeAttr('disabled');
                    filesList = null;
                }
            }

            $('.fbxx').fileupload(settings);
        });
    </script>
@endsection


