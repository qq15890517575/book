<?php

namespace App\Http\Controllers\Service;

use App\Entity\Member;
use App\Entity\TempEmail;
use App\Entity\TempPhone;
use App\Models\M3Email;
use App\Models\M3Result;
use App\Tool\UUID;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class MemberController extends Controller
{
    public function register(Request $request)
    {
        $email = $request->input('email', '');
        $phone = $request->input('phone', '');
        $password = $request->input('password', '');
        $confirm = $request->input('confirm', '');
        $phone_code = $request->input('phone_code', '');
        $validate_code = $request->input('validate_code', '');
        $m3_result = new M3Result();
        $member = Member::where('email',$email)->orWhere('phone',$phone)->first();
        if($member) {
            $m3_result->status = 5;
            $m3_result->message = '对不起，已被注册';
            return $m3_result->toJson();
        }
        if ($email == '' && $phone == '') {
            $m3_result->status = 1;
            $m3_result->message = '手机号或邮箱不能为空';
            return $m3_result->toJson();
        }

        if ($password == '' || strlen($password) < 6 || strlen($password) > 20) {
            $m3_result->status = 2;
            $m3_result->message = '密码必须是6-20个字符';
            return $m3_result->toJson();
        }


        if ($confirm == '' || strlen($confirm) < 6 || strlen($password) > 20) {
            $m3_result->status = 3;
            $m3_result->message = '确认密码长度必须是6-20个字符';
            return $m3_result->toJson();
        }

        if ($password != $confirm) {
            $m3_result->status = 4;
            $m3_result->message = '两次密码不一致';
            return $m3_result->toJson();
        }


        // 手机注册
        if ($phone != '') {
            if ($phone_code == '' || strlen($phone_code) != 6) {
                $m3_result->status = 4;
                $m3_result->message = '两次密码不一致';
                return $m3_result->toJson();
            }

            $tempPhone = TempPhone::where('phone', $phone)->first();
            // 如果提交过来的验证码和数据库里的验证码相同，那么就判断是否过期
            if ($tempPhone->code == $phone_code) {
                if (time() > strtotime($tempPhone->deadline)) {
                    $m3_result->status = 7;
                    $m3_result->message = '手机验证码已失效';
                    return $m3_result->toJson();
                }
                $member = new Member;
                $member->phone = $phone;
                $member->password = md5('bk' . $password);
                $member->save();
                $m3_result->status = 0;
                $m3_result->message = '注册成功';
                return $m3_result->toJson();
            } else {
                $m3_result->status = 7;
                $m3_result->message = '手机验证码不正确';
                return $m3_result->toJson();
            }


        } else {
            if ($validate_code == '' || strlen($validate_code) != 4) {
                $m3_result->status = 6;
                $m3_result->message = '验证码为4位';
                return $m3_result->toJson();
            }

            $validate_code_session = $request->session()->get('validate_code', '');
            if ($validate_code_session != $validate_code) {
                $m3_result->status = 8;
                $m3_result->message = '验证码不正确';
                return $m3_result->toJson();
            }

            $member = new Member;
            $member->email = $email;
            $member->password = md5('bk' . $password);
            $member->save();

            $uuid = UUID::create();
            //使用M3Email这个类
            $m3_email = new M3Email();
            $m3_email->to = $email;
            $m3_email->cc = 'yqj666888@yeah.net';
            $m3_email->subject = '凯恩书店';
            $m3_email->content = '请于24小时点击该链接完成验证http://'.$_SERVER['SERVER_NAME'].'/api/service/validate_email'
                . '?member_id=' . $member->id
                . '&code=' . $uuid;;
            $tempEmail = new TempEmail();
            $tempEmail->member_id = $member->id;
            $tempEmail->code = $uuid;
            $tempEmail->deadline = date('Y-m-d H-i-s', time() + 24 * 60 * 60);
            $tempEmail->save();
            Mail::to($email)->send(new \App\Mail\WelcomeToBook($m3_email));

        }
        $m3_result->status = 0;
        $m3_result->message = '注册成功';
        return $m3_result->toJson();
    }

    //登陆验证
    public function login(Request $request)
    {
        $username = $request->get('username', '');
        $password = $request->get('password', '');
        $validate_code = $request->get('validate_code', '');
        $m3_result = new M3Result();
        if ($validate_code != $request->session()->get('validate_code')) {
            $m3_result->status = 4;
            $m3_result->message = '验证码不正确。';
            return $m3_result->toJson();
        }

        $member = null;
        if (strpos($username, '@') == true) {
            $member = Member::where('email', $username)->first();
        } else {
            $member = Member::where('phone', $username)->first();
        }
        if ($member == null) {
            $m3_result->status = 2;
            $m3_result->message = '该用户不存在';
            return $m3_result->toJson();
        } else {
            if (strpos($username, '@') == true && $member->cative == 0) {
                $m3_result->status = 5;
                $m3_result->message = '邮箱尚未激活。';
                return $m3_result->toJson();
            }
            if (md5('bk' . $password) != $member->password) {
                $m3_result->status = 3;
                $m3_result->message = '密码不正确';
                return $m3_result->toJson();
            }
        }
        $request->session()->put('member', $member);
        $m3_result->status = 0;
        $m3_result->message = '登陆成功';
        return $m3_result->toJson();
    }
}
