<?php

namespace App\Models;

class M3Email
{
    public $from;  // 发件人邮箱
    //如果收件人是一个人，那么可以指定为字符串，如果是多个人，则指定为数组
    public $to; // 收件人邮箱
    public $cc; // 抄送
    public $attach; // 附件
    public $subject; // 主题
    public $content; // 内容
}
