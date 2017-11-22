<?php

namespace App\Http\Controllers\Admin;

use App\Entity\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function login()
    {
        return redirect('admin/index');
    }

    public function toLogin()
    {
        return view('admin.login');
    }
    public function toIndex()
    {
        return view('admin.index');
    }

    public function toCategory()
    {
        $categorys = Category::all();
        foreach ($categorys as $category) {
            if($category->parent_id != null && $category->parent_id !='') {
                $category->parent = Category::find($category->parent_id);
            }
        }
        return view('admin.category')->with('categorys',$categorys);
    }
}
