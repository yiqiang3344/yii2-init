<?php

namespace common\logging;

use yii\base\Model;

/**
 * 业务日志
 */
class BizLog extends Model
{
    /**
     * 记日志
     * @param $message
     * @param string $message_tag
     * @param ILogObject|null $object
     */
    public function log($message, $message_tag = '', ILogObject $object = null)
    {
        $params = ['message' => $message];
        $params['message_tag'] = $message_tag;
        if ($object) {
            $params = array_merge($params, $object->getLogExtraParams());
        }
        \Yii::info($params, 'biz');
    }
}