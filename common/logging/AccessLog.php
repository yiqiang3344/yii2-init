<?php
namespace common\logging;

use yii\base\Model;

/**
 * 访问日志
 */
class AccessLog extends Model
{
    public static function log($message)
    {
        \Yii::info($message, 'access');
    }
}