<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/11
 * Time: 11:22 AM
 */

namespace common\logging;

use yii\base\Model;

/**
 * 短信日志
 * Class AdminTraceLog
 * @package common\logging
 */
class SmsLog extends Model
{
    /**
     * 记日志
     * @param $message
     * @param string $message_tag
     * @param ILogObject|null $object
     */
    public static function log($message, $message_tag, ILogObject $object = null)
    {
        $params = ['message' => $message];
        $params['message_tag'] = $message_tag;
        if ($object) {
            $params = array_merge($params, $object->getLogExtraParams());
        }
        \Yii::info($params, 'user_cash');
    }
}