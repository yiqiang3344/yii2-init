<?php
/**
 * Created by PhpStorm.
 * User: sidney
 * Date: 2019/11/5
 * Time: 4:38 PM
 */

namespace common\facade;

use common\helper\Env;
use yiqiang3344\yii2_lib\commonSms\SmsCenter;
use yiqiang3344\yii2_lib\helper\Time;
use yii\base\Exception;


/**
 * 消息中心
 * User: sidney
 * Date: 2020/1/6
 */
class CommonSms extends SmsCenter
{
    protected function log($message, $tag)
    {
    }

    /**
     * 发送错误通知
     * @param $subject
     * @param $content
     * @param $addressBook
     * @return bool
     * @throws Exception
     */
    public function sendErrorNotify($subject, $content, $addressBook, $config = [])
    {
        return $this->sendNotify($subject, $content, $addressBook, ['common_error_notify'], $config);
    }

    /**
     * 根据异常发送错误通知
     * @param \Throwable $e
     * @return bool
     * @throws Exception
     */
    public function sendErrorNotifyByException(\Throwable $e)
    {
        $subject = Env::getErrorNotifySubjectPre() . "#{$e->getFile()}:{$e->getLine()}";
        $content = Time::now() . ' (' . get_class($e) . ')' . '[' . $e->getCode() . '] ' . $e->getMessage() . PHP_EOL . '#' . $e->getFile() . ':' . $e->getLine() . PHP_EOL . $e->getTraceAsString() . PHP_EOL . 'request-flow-number:' . Env::getRequestFloatNumber();
        return $this->sendErrorNotify($subject, $content, Config::instance()->getMonitorAddressBook());
    }

    /**
     * 发送错误通知
     * @param $method
     * @param $args
     * @param array $header
     * @param array $options
     * @param array $config
     * @return bool
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function sendSms($method, $args, $header = [], $options = [], $config = [])
    {
        return self::requestSmsCenter($method, $args, $header, $options, $config);
    }
}