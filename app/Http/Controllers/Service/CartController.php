<?php

namespace App\Http\Controllers\Service;

use App\Entity\CartItem;
use App\Models\M3Result;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    public function addCart(Request $request, $product_id)
    {
        $m3_result = new M3Result();
        $m3_result->status = 0;
        $m3_result->message = '添加成功';

        // 如果当前已经登录
        $member = $request->session()->get('member', '');
        if ($member != '') {
            // 从购物车表中取出这个用户的购物车信息
            $cart_items = CartItem::where('member_id', $member->id)->get();
            $exist = false;
            // 循环判断，这个用户的购物车中是否有这个商品信息
            foreach ($cart_items as $cart_item) {
                // 如果有，就对购物车中的数量进行+1，并将标示$exist改为true
                if ($cart_item->product_id == $product_id) {
                    $cart_item->count++;
                    $cart_item->save();
                    $exist = true;
                    break;
                }
            }
            // $exist == false 表示用户没有添加这个商品到购物车，那么就加入到购物车
            if ($exist == false) {
                $cart_item = new CartItem;
                $cart_item->product_id = $product_id;
                $cart_item->count = 1;
                $cart_item->member_id = $member->id;
                $cart_item->save();
            }

            return $m3_result->toJson();
        }

        // cookie里的存储形式是这样的：1:2,2:4,3:1,5,2........
        $bk_cart = $request->cookie('bk_cart');
        // return $bk_cart;
        // 如果从cookie取出的$bk_cart不是null就截取，得到一个数组
        $bk_cart_arr = ($bk_cart != null ? explode(',', $bk_cart) : array());

        $count = 1;
        // $bk_cart_arr这个数组的形式，应该是这样的： array('1:2','2:4','3:1','5,2'.......)
        // 一定要传引用进来，这是为了，当我们在下面改变$value的值时，数组里的值就会被改变
        // 因为数组是基本类型，默认是传值赋值，如果你想要改变数组里的值，就需要使用引用赋值&，，如果是对象则可以不用这么做，因为对象就是引用赋值
        foreach ($bk_cart_arr as &$value) {
            // 循环后$value应该是这样的，如1:2
            // strpos()查找字符串首次出现的位置
            $index = strpos($value, ':');

            // substr($value,0,$index) 就是:之前的，产品id
            // 如果cookie取到的产品id和传来的相等，说明购物车中存在这个商品
            if (substr($value, 0, $index) == $product_id) {
                // substr($value,$index+1) 就是:之后的这个产品的数量.$index+1是为了取:之后的数量
                // 对数量进行+1 ,  得到的是字符串，将其强转成int类型
                $count = ((int)substr($value, $index + 1)) + 1;
                // 组成新的键值对
                $value = $product_id . ':' . $count;
                break;
            }
        }

        // 如果$count 的值还是1，没有改变，说明购物车没有这件商品，那么我们就把它加入到购物车。
        if ($count == 1) {

            array_push($bk_cart_arr, $product_id . ':' . $count);
        }

        // cookie里面只能存字符串
        // 把新组成的数组，拆分成字符串，存储到cookie里面
        return response($m3_result->toJson())->withCookie('bk_cart', implode(',', $bk_cart_arr));
    }

    /**
     * 购物车的删除操作
     * @param Request $request
     * @return json
     */
    public
    function deleteCart(Request $request)
    {
        $m3_result = new M3Result;
        $m3_result->status = 0;
        $m3_result->message = '删除成功';

        // product_ids 是前台ajax传来的数据，里面包含了选中商品的id
        $product_ids = $request->input('product_ids', '');
        if ($product_ids == '') {
            $m3_result->status = 1;
            $m3_result->message = '书籍ID为空';
            return $m3_result->toJson();
        }
        $product_ids_arr = explode(',', $product_ids);

        $member = $request->session()->get('member', '');
        if ($member != '') {
            // 已登录
            CartItem::whereIn('product_id', $product_ids_arr)->delete();
            return $m3_result->toJson();
        }

        // 未登录
        $bk_cart = $request->cookie('bk_cart');
        $bk_cart_arr = ($bk_cart != null ? explode(',', $bk_cart) : array());
        foreach ($bk_cart_arr as $key => $value) {
            $index = strpos($value, ':');
            $product_id = substr($value, 0, $index);
            // 存在, 删除
            if (in_array($product_id, $product_ids_arr)) {
                array_splice($bk_cart_arr, $key, 1);
                continue;
            }
        }
        // 再将数组转化成字符串，储存到cookie里面
        return response($m3_result->toJson())->withCookie('bk_cart', implode(',', $bk_cart_arr));
    }
}
