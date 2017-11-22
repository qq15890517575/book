<?php

namespace App\Http\Controllers\View;

use App\Entity\Category;
use App\Entity\PdtContent;
use App\Entity\PdtImages;
use App\Entity\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookController extends Controller
{
    public function toCategory(Request $request)
    {
        $categorys = Category::where('parent_id')->get();
        return view('category')->with('categorys', $categorys);
    }

    public function toProduct($category_id)
    {
        $products = Product::where('category_id', $category_id)->get();
        return view('product')->with('products', $products);
    }

    public function toPdtContent(Request $request,$product_id)
    {
        $bk_cart = $request->cookie('bk_cart');
        $bk_cart_arr = ($bk_cart != null ? explode(',',$bk_cart) : array());
        $count = 0;
        foreach ($bk_cart_arr as $value) {
            $index = strpos($value,':');
            if(substr($value,0,$index) == $product_id) {
                $count = ((int) substr($value,$index+1));
                break;
            }
        }

        $product = Product::find($product_id);
        $pdt_content = PdtContent::where('product_id', $product_id)->first();
        $pdt_images = PdtImages::where('product_id', $product_id)->get();
        return view('pdt_content')->with('product', $product)
                                  ->with('pdt_images', $pdt_images)
                                  ->with('count', $count)
                                  ->with('pdt_content', $pdt_content);
    }
}
