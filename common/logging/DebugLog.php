<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/8/22
 * Time: 4:30 PM
 */

namespace common\logging;


use yii\base\Model;

/**
 * 调试日志
 * @method void error(mixed $message, string $message_tag = '') 错误日志
 * @method void info(mixed $message, string $message_tag = '') 信息日志
 * @method void warning(mixed $message, string $message_tag = '') 警告日志
 */
class DebugLog extends Model
{

    /**
     * 记日志
     * @param $message
     * @param string $message_tag
     */
    public function log($message, $message_tag = '')
    {
        $this->info($message, $message_tag);
    }

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        if (in_array($name, ['error', 'warning', 'info'])) {
            $message = isset($args[1]) ? ['message_tag' => $args[1], 'message' => $args[0]] : $args[0];
            return call_user_func_array(['\\Yii', $name], [$message, 'debug']);
        }
        return parent::__call($name, $args);
    }
}