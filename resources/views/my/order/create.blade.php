@extends('layouts.master')
@section('title', '微信支付')
@section('body-attr', 'class="weipay"')
@section('body')
    <h3 class="greeting">您好！{{$user['name']}}</h3>
    <p class="mb6">用户名：{{hideMobile($mobile)}}</p>
    <p class="mb6">您正在购买<span class="bold">{{$analyst['name']}}</span>的VIP服务</p>

    <!-- 产品价格 -->
    <p class="mb6">产品价格：<span class="price yellow">{{$price}}</span>元/月</p>
    <p>购买VIP服务后，您可以在服务期限内观看该分析师的VIP服务内容（培训和策略）。</p>

    <table width="100%" class="buy bdb bdt">
        <tr>
            <th width="50%">支付金额</th>
            <th width="50%">购买数量</th>
        </tr>
        <tr>
            <!-- 支付总价 -->
            <td width="50%"><span class="total"></span>元</td>
            <td width="50%">
                <img class="jian vm" src="/img/jian.svg" alt='' width="16px">
                <span class="amount pl10 pr10">1</span>
                <img class="jia vm" src="/img/jia.svg" alt='' width="16px">
            </td>
        </tr>
    </table>
    <p class="f16">您的服务期限为：<span class="begin">{{$now->format('Y-m-d')}}</span>至<span class="end">{{$end->format('Y-m-d')}}</span></p>
    <button class="btn-lg weipay" id="weipay" data-type="weipay" data-product-id="{{$analyst['id']}}" data-month="1">
            <img src="/img/weixin.svg" class="vm" alt="" width="28px">
            微信支付
    </button>

@endsection
@section('footer_nav')
@endsection
@section('footer')
    <script src="/js/xdate.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>

    <script>

        // 获取价格
        var price = parseInt($(".price").text());
        $(".total").text(price);
        var end = new XDate("{{$end->format('Y-m-d')}}");
        var myDatetime = function(count){
            end.addMonths(count);
            $('.end').text(end.toString('yyyy-MM-dd'));
        }
        // 数量加减
        $(".jia").click(function(){
            var amount = $(".amount");
            var num = parseInt(amount.text());
            if(num==12){
                return ;
            }
            num = num + 1;
            amount.text(num);
            $('#weipay').attr('data-month',num)
            $(".total").text(num * price);
            myDatetime(1);
        });
        $(".jian").click(function(){
            var amount = $(".amount");
            var num = parseInt(amount.text());
            if(num == 1){
                return;
            }else{
                num = num - 1;
                amount.text(num);
                $('#weipay').attr('data-month',num)
                $(".total").text(num * price);
                myDatetime(-1);
            }
        });
    </script>

    <script type="text/javascript" charset="utf-8">
        @if($isWechat)
        wx.config(<?php echo $js->config(array('chooseWXPay'), false) ?>);
        $('#weipay').click(function(){
            var _this = $(this);
            var type = $(this).attr('data-type');
            var product_id = $(this).attr('data-product-id');
            var product_type = $(this).attr('data-product-type');
            var month = $(this).attr('data-month');
            if(product_id<1){
                layer.msg('产品不能为空');
                return false;
            }
            $.post('/my/order',
                    {type:type,product_id:product_id,product_type:product_type,month:month},
                    function (data) {

                wx.chooseWXPay({
                    timestamp: data.timestamp,
                    nonceStr: data.nonceStr,
                    package: data.package,
                    signType: data.signType,
                    paySign: data.paySign, // 支付签名
                    success: function (res) {
                        layer.msg('支付成功');
                        location.href = '/my/order/'+data.order_id;
                    }
                });
            });
        });
        @else
            $('#weipay').click(function(){layer.msg('电脑端不支付微信支付');});
        @endif
    </script>
@endsection