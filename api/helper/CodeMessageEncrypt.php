<?php

namespace api\helper;


use common\facade\Config;
use common\helper\CodeMessage;
use yiqiang3344\yii2_lib\helper\response\TEcryptResponse;

class CodeMessageEncrypt extends CodeMessage
{
    use TEcryptResponse;

    public static function getResponseEncryptSwitch(): bool
    {
        return Config::instance()->getApiResponseEncrypt();
    }
}