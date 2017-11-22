<?php

namespace App\Http\Controllers\Service;

use App\Entity\Category;
use App\Models\M3Result;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookController extends Controller
{
    public function getCategoryByParentId($parent_id)
    {
        $categorys = Category::where('parent_id',$parent_id)->get();
        $m3_result = new M3Result();
        $m3_result->status = 0;
        $m3_result->message = '返回成功';
        // 弱语言的特性，可以随时定义成员变量，但是少用，这种效率低
        $m3_result->categorys = $categorys;
        return $m3_result->toJson();
    }
}
