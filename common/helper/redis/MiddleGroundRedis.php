<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/11
 * Time: 11:22 AM
 */

namespace common\helper\redis;


/**
 * 现金贷Redis
 * Class UserLog
 * @package common\unit
 */
class MiddleGroundRedis extends \yiqiang3344\yii2_lib\helper\redis\Redis
{
    protected static $redisName='middle_ground_redis';
    protected static $redis;

    public static function getJwtKey($mobile, $appName)
    {
        return "jwt:{$mobile}:{$appName}";
    }
}