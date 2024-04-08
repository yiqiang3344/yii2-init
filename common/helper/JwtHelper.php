<?php


namespace common\helper;


use common\exception\CUserException;
use common\helper\redis\MiddleGroundRedis;
use yiqiang3344\yii2_lib\helper\UserJwt;
use yii\base\Exception;
use yii\base\UserException;
use yii\redis\Connection;

class JwtHelper extends UserJwt
{
    /**
     * @return Connection
     */
    public static function getRedis()
    {
        return MiddleGroundRedis::instance();
    }

    /**
     * 校验并解析token，成功则返回payload的信息
     * @param $token
     * @param null $app
     * @return array
     * @throws CUserException|Exception
     */
    public static function validateToken($token, $app = null)
    {
        try {
            return parent::validateToken($token, $app);
        } catch (UserException $e) {
            throw new CUserException('用户登录失败:' . $e->getMessage());
        }
    }
}