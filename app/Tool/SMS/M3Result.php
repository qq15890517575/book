<?php

class M3Result {
  //状态，一般情况下成功$status=0
  public $status;
  //返回信息
  public $message;

  public function toJson()
  {
      //把这个对象转换成json字符串
      //JSON_UNESCAPED_UNICODE  这个参数必须带，不然中文不能正常显示
    return json_encode($this, JSON_UNESCAPED_UNICODE);
  }
}
