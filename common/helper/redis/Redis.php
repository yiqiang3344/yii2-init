<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/11
 * Time: 11:22 AM
 */

namespace common\helper\redis;

use common\helper\Env;

/**
 * 通用Redis
 * Class UserLog
 * @package common\unit
 */
class Redis extends \yiqiang3344\yii2_lib\helper\redis\Redis
{
    protected static $redisName = 'redis';
    protected static $redis;

    const PRE = PROJECT_NAME;

    /**
     * 统一生成key的方法
     * @param $key
     * @param array $params
     * @return string
     */
    public static function generateKey($key, $params = [])
    {
        return self::PRE . ':' . $key . ($params ? ':' . implode(':', $params) : '');
    }

    /**
     * 统一生成对应应用的key的方法
     * @param $key
     * @param array $params
     * @return string
     */
    public static function generateAppKey($key, $params = [])
    {
        return self::PRE . ':' . Env::getApp() . ':' . $key . ($params ? ':' . implode(':', $params) : '');
    }

    /**
     * 短信验证码 过期 key
     * @param $mobile
     * @param $appName
     * @return string
     */
    public static function getSmsCaptchaKey($mobile, $appName)
    {
        return self::PRE . ':' . "captcha:$appName:{$mobile}:expire:300";
    }

    /**
     * 短信验证码 限制 key
     * @param $mobile
     * @param $appName
     * @return string
     */
    public static function getSmsLimitKey($mobile, $appName)
    {
        return self::PRE . ':' . "captcha:$appName:{$mobile}:limits:60";
    }
}