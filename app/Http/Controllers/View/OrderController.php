<?php

namespace App\Http\Controllers\View;

use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function toOrderCommit(Request $request)
    {
        $product_ids = $request->route('product_ids', '');
        $product_ids_arr = ($product_ids != '' ? explode(',', $product_ids) : array());

        $member = $request->session()->get('member', '');
        // 从CartItem表中取出所有product_id的值在$product_ids_arr数组中的数据
        $cart_items = CartItem::where('member_id', $member->id)->whereIn('product_id', $product_ids_arr)->get();
        $cart_items_arr = array();
        $total_price = 0;
        foreach ($cart_items as $cart_item) {
            // 给$cart_item添加一个product属性，里面存储这个商品的信息，仅用于页面显示
            $cart_item->product = Product::find($cart_item->product_id);
            // 如果能根据商品id查询到商品信息，就把这条购物车的记录，储存到数组里面。
            if ($cart_item->product != null) {
                // $cart_item 表示购物车中的每条商品记录，$cart_item->count 购物车中这个商品的数量
                // $cart_item->product->price 单个商品的价格
                // 把每次循环的结果进行累加，就是所有商品的总价格
                $total_price += $cart_item->product->price * $cart_item->count;
                array_push($cart_items_arr, $cart_item);
            }
        }
        return view('order_commit')->with('cart_items', $cart_items_arr)
            ->with('total_price', $total_price);
    }

    public function toOrderList(Request $request)
    {
        //从session中读取到用户信息
        $member = $request->session()->get('member', '');
        // 获取到这个用户的订单
        $orders = Order::where('member_id', $member->id)->get();
        // 循环订单
        foreach ($orders as $order) {
            $order_items = OrderItem::where('order_id', $order->id)->get();
            // 给每一个订单 关联上order_item表中对应的信息，赋给对象的一个属性
            $order->order_items = $order_items;
            // 再order_item中的每一条记录，给每一个记录添加上这个商品的商品信息，便于显示。
            foreach ($order_items as $order_item) {
                $order_item->product = Product::find($order_item->product_id);
            }
        }
        // 此时的$order其实已经包含了order_list表和product表中的信息
        return view('order_list')->with('orders', $orders);
    }
}
