<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/8/29
 * Time: 5:27 PM
 */

namespace common\models;


use common\logging\BizLog;
use common\logging\ILogObject;

trait TLog
{
    /**
     * 记日志
     * @param $message
     * @param string $message_tag
     * @param ILogObject|null $object
     */
    public function log($message, $message_tag = '', ILogObject $object = null)
    {
        BizLog::instance()->log($message, $message_tag, $object);
    }
}