@extends('master')

@section('title', '订单提交')

@section('content')
 <div class="page bk_content" style="top: 0;">
  <div class="weui_cells">
   @foreach($cart_items as $cart_item)
    <div class="weui_cell">
     <div class="weui_cell_hd">
      <img src="{{$cart_item->product->preview}}" alt="" class="bk_icon">
     </div>
     <div class="weui_cell_bd weui_cell_primary">
      <p class="bk_summary">{{$cart_item->product->name}}</p>
     </div>
     <div class="weui_cell_ft">
      <span class="bk_price">{{$cart_item->product->price}}</span>
      <span> x </span>
      <span class="bk_important">{{$cart_item->count}}</span>
     </div>
    </div>
   @endforeach
  </div>
  <div class="weui_cells_title">支付方式</div>
  <div class="weui_cells">
   <div class="weui_cell weui_cell_select">
    <div class="weui_cell_bd weui_cell_primary">
     <select class="weui_select" name="payway">
      <option selected="" value="1">支付宝</option>
      <option value="2">微信</option>
     </select>
    </div>
   </div>
  </div>

  <form action="/service/alipay" id="alipay" method="post">
   {{ csrf_field() }}
   <input type="hidden" name="total_price" value="" />
   <input type="hidden" name="name" value="" />
   <input type="hidden" name="order_no" value="" />
  </form>

  <div class="weui_cells">
   <div class="weui_cell">
    <div class="weui_cell_bd weui_cell_primary">
     <p>总计:</p>
    </div>
    <div class="weui_cell_ft bk_price" style="font-size: 25px;">￥ {{ $total_price }}</div>
   </div>
  </div>
 </div>
 <div class="bk_fix_bottom">
  <div class="bk_btn_area">
   <button class="weui_btn weui_btn_primary" onclick="_onPay();">提交订单</button>
  </div>
 </div>

@endsection

@section('my-js')
 <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" charset="utf-8"></script>
 <script type="text/javascript">
     wx.config({
         debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。

         jsApiList: ['chooseWXPay'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
     });
     wx.ready(function(){
         // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
     });
     wx.error(function(res){
         // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。

     });


 </script>
@endsection
