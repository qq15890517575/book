<?php

namespace App\Http\Controllers\View;

use App\Entity\CartItem;
use App\Entity\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function toCart(Request $request)
    {
        $cart_items = array();

        $bk_cart = $request->cookie('bk_cart');
        // 拆分cookie中存储的键值对，形成数组
        $bk_cart_arr = ($bk_cart != null ? explode(',', $bk_cart) : array());

        $member = $request->session()->get('member', '');

        // $member不为空，表示已经登录
        if ($member != '') {
            // syncCart()方法 获取数据库里的购物车信息
            $cart_items = $this->syncCart($member->id, $bk_cart_arr);
            // 已经登录，就操作数据库里的购物车，清空cookie里面的购物车信息
            return response()->view('cart', ['cart_items' => $cart_items])->withCookie('bk_cart', null);
        }

        // 否则就是没有登录，不需要查询数据库了，，只需要取出cookie中的购物车数据即可
        foreach ($bk_cart_arr as $key => $value) {
            $index = strpos($value, ':');
            $cart_item = new CartItem;
            $cart_item->id = $key;
            $cart_item->product_id = substr($value, 0, $index);
            $cart_item->count = (int)substr($value, $index + 1);
            $cart_item->product = Product::find($cart_item->product_id);
            if ($cart_item->product != null) {
                array_push($cart_items, $cart_item);
            }
        }

        return view('cart')->with('cart_items', $cart_items);
    }

    private function syncCart($member_id, $bk_cart_arr)
    {
        // 获取此用户在购物车表里的数据
        $cart_items = CartItem::where('member_id', $member_id)->get();

        $cart_items_arr = array();

        // 循环取出本地cookie中存取的购物车数据
        foreach ($bk_cart_arr as $value) {
            $index = strpos($value, ':');
            // 获取 : 之前的商品id
            $product_id = substr($value, 0, $index);
            // 获取 : 之后的商品数量
            $count = (int)substr($value, $index + 1);

            // 判断离线购物车中product_id 是否存在 数据库中
            $exist = false;
            foreach ($cart_items as $temp) {
                // 如果存在
                if ($temp->product_id == $product_id) {
                    // 如果数据库购物车里此商品的数量，少于本地存储的数量
                    if ($temp->count < $count) {
                        // 就将数据库里的数量改为本地的数量
                        $temp->count = $count;
                        $temp->save();
                    }
                    $exist = true;
                    break;
                }
            }

            // $exist == false  表示没有走上面if中的代码，也就会不存在
            // 不存在则存储进来
            if ($exist == false) {
                $cart_item = new CartItem;
                $cart_item->member_id = $member_id;
                $cart_item->product_id = $product_id;
                $cart_item->count = $count;
                $cart_item->save();
                // 得到这条购物车记录，在产品表中对应的产品的信息，方便在页面显示
                $cart_item->product = Product::find($cart_item->product_id);
                // 购物车每条记录的信息和这条记录对应的产品信息都存储到新数组中
                array_push($cart_items_arr, $cart_item);
            }
        }

        // 为每个对象附加产品对象便于显示
        foreach ($cart_items as $cart_item) {
            $cart_item->product = Product::find($cart_item->product_id);
            array_push($cart_items_arr, $cart_item);
        }

        return $cart_items_arr;
    }
}
