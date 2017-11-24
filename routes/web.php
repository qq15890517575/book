<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

Route::get('/', 'View\BookController@toCategory');

// 后台图片上传
Route::group(['prefix' => 'service'], function () {
    Route::post('upload/{type}', 'Service\UploadController@uploadFile');
});

Route::group(['namespace'=>'View'],function() {
    // 登录页面显示
    Route::get('/login','MemberController@toLogin');
    // 注册页面显示
    Route::get('/register', 'MemberController@toRegister');
    // 书籍类别显示
    Route::get('/category', 'BookController@toCategory');
    // 书籍列表显示
    Route::get('/product/category_id/{category_id}', 'BookController@toProduct');
});

Route::group(['middleware'=>'check.login'], function() {
    Route::get('/order_commit/{product_ids}', 'View\OrderController@toOrderCommit');
    Route::get('/order_list', 'View\OrderController@toOrderList');
});

// 商品详情
Route::get('/product/{product_id}', 'View\BookController@toPdtContent');

// 购物车页面
Route::get('/cart', 'View\CartController@toCart');


Route::get('/pay', function() {
    return view('alipay');
});

Route::group(['prefix'=>'admin', 'namespace'=>'Admin'], function() {
    Route::group(['prefix'=>'service'], function() {
        Route::post('/login', 'IndexController@login');
        Route::post('category/add', 'CategoryController@categoryAdd');
        Route::post('category/del', 'CategoryController@categoryDel');
        Route::post('category/edit', 'CategoryController@categoryEdit');
    });
    Route::get('index', 'IndexController@toIndex');
    Route::get('category', 'CategoryController@toCategory');
    Route::get('category_add', 'CategoryController@toCategoryAdd');
    Route::get('category_edit', 'CategoryController@toCategoryEdit');
    Route::get('login', 'IndexController@toLogin');
});


