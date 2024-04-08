<?php
/**
 * Created by PhpStorm.
 * User: sidney
 * Date: 2020/1/14
 * Time: 11:59 AM
 */

namespace common\helper\encrypt;


class Encrypt extends \yiqiang3344\yii2_lib\helper\encrypt\Encrypt
{
    public static function rsaEncrypt($source, $type, $key)
    {
        return base64_encode(parent::rsaEncrypt($source, $type, $key));
    }

    public static function rsaDecrypt($source, $type, $key)
    {
        return parent::rsaDecrypt(base64_decode($source), $type, $key);
    }

    public static function rsaDecryptForUrlEncodeJson($source, $type, $key)
    {
        $ret = parent::rsaDecrypt(base64_decode($source), $type, $key);
        if (strpos($ret, '%7B') === 0) {
            return urldecode($ret);
        }
        return $ret;
    }
}