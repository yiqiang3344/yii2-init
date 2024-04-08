<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/11
 * Time: 11:22 AM
 */

namespace console\logging;

use yii\base\Model;

/**
 * 处理日志
 */
class ConsoleLog extends Model
{
    public static function log($message)
    {
        \Yii::info($message, 'console');
    }
}