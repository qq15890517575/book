<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix' => '/service','namespace' => 'Service'], function () {
    // 验证码
    Route::get('validate_code/create', 'ValidateController@create');
    // 发送短信
    Route::post('validate_phone/send', 'ValidateController@sendSMS');
    // 邮箱激活验证
    Route::get('validate_email','ValidateController@validateEmail');
    // 登陆验证
    Route::post('login', 'MemberController@login');
    // 注册验证
    Route::post('register', 'MemberController@register');

    Route::get('category/parent_id/{parent_id}', 'BookController@getCategoryByParentId');
    // 添加到购物车
    Route::get('cart/add/{product_id}','CartController@addCart');
    // 购物车的删除操作
    Route::get('cart/delete','CartController@deleteCart');
    // 唤起支付宝支付
    Route::post('pay','PayController@alipay');
    // 支付宝支付回调
    Route::post('pay/ali_notify', 'PayController@aliNotify');
    // 支付宝确认同步转跳页面
    Route::get('pay/call_back', 'PayController@callBack');
    // 支付宝确认支付中断页面
    Route::get('pay/merchant', 'PayController@merchant');

});


Route::group(['middleware'=>'check.login'], function() {

});


