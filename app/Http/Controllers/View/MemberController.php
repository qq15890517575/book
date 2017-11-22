<?php

namespace App\Http\Controllers\View;

use App\Tool\Validate\Validate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MemberController extends Controller
{
    public function toLogin(Request $request)
    {
        $validateCode = new Validate();
        $request->session()->put('validate_code',$validateCode->getCode());
        $returnUrl = $request->input('return_url','');
        return view('login')->with('returnUrl',urldecode($returnUrl));
    }

    public function toRegister()
    {
        return view('register');
    }
}
