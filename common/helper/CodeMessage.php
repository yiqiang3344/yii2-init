<?php

namespace common\helper;

class CodeMessage extends \yiqiang3344\yii2_lib\helper\CodeMessage
{
    public static $codeMap = [
        '1' => 'success',
        '-1' => '',
        '-2' => '', //header异常
        '-3' => '', //参数校验异常
        '-30' => '', //数据库操作异常

        #########系统异常##########
        '-20' => 'system error',

        ##############
        ##backend
        ##############
        //用户
        '200000' => '请重新登录',
        '200010' => '无权操作',
        '200020' => '密码已过期',
        '200030' => '账号已禁用',

    ];
}