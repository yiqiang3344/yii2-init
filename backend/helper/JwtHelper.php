<?php

namespace backend\helper;


use common\exception\CUserException;
use common\helper\redis\Redis;
use yiqiang3344\yii2_lib\helper\UserJwt;
use yii\base\UserException;
use yii\redis\Connection;

class JwtHelper extends UserJwt
{
    /**
     * @return Connection
     */
    public static function getRedis()
    {
        return Redis::instance();
    }

    /**
     * @param $uid
     * @param $app
     * @return string
     */
    protected static function generateKey($uid, $app)
    {
        return Redis::generateKey("jwt", [$uid]);
    }

    public static function clearByUID($uid)
    {
        static::clearTokenCache($uid, null);
        return true;
    }

    /**
     * @inheritDoc
     */
    public static function generateToken($uid, $app, $params = [], $head = [])
    {
        return parent::generateToken($uid, $app, $params, $head);
    }

    /**
     * @inheritDoc
     */
    public static function validateToken($token, $app = null)
    {
        try {
            return parent::validateToken($token, $app);
        } catch (UserException $e) {
            throw new CUserException($e->getMessage(), 200000);
        }
    }
}