<?php

namespace common\facade;

use common\helper\Env;
use common\helper\redis\Redis;
use Yii;
use yii\base\UserException;

class Captcha
{
    private static function getLimitKey($mobile, $bizType)
    {
        return Redis::generateKey("captcha:$bizType:$mobile:limits:60");
    }

    private static function getExpireKey($mobile, $bizType)
    {
        return Redis::generateKey("captcha:$bizType:$mobile:expire:300");
    }

    public static function sendSmsCode($mobile, $bizType)
    {
        $limitKey = self::getLimitKey($mobile, $bizType);
        $redis = Redis::instance();
        $value = $redis->get($limitKey);
        if ($value) {
            throw new UserException("{$mobile} : 获取短信次数太频繁，请稍后再试");
        }

        $smsExpireKey = self::getExpireKey($mobile, $bizType);
        $captcha = mt_rand(1000, 9999);
        if (Env::isTest()) {
            $captcha = '1234';
            //短信5分钟有效
            $redis->setex($limitKey, 60, $captcha);
            $redis->setex($smsExpireKey, 300, $captcha);
            return true;
        }
        //短信5分钟有效
        $redis->setex($limitKey, 60, $captcha);
        $redis->setex($smsExpireKey, 300, $captcha);

        Sms::send($mobile, 'smstpl588746', [
            '#code' => $captcha,
        ], 'xyf', 'xyf');

        return true;
    }

    /**
     * 验证验证码
     * @param $mobile
     * @param $bizType
     * @param $captcha
     * @throws UserException
     */
    public static function validate($mobile, $bizType, $captcha)
    {
        $smsExpireKey = self::getExpireKey($mobile, $bizType);
        $redis = Redis::instance();
        $cacheCaptcha = $redis->get($smsExpireKey);
        if (empty($cacheCaptcha)) {
            throw new UserException('验证码已过期');
        }
        if ($cacheCaptcha != $captcha) {
            throw new UserException('验证码不正确');
        }
    }
}