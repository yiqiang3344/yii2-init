<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/6/19
 * Time: 11:53 AM
 */

namespace common\helper;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use common\facade\Config;
use Yii;

class QueueHelper
{
    //交换机
    const EXCHANGE_EMAIL = 'sms_email'; //邮件

    //队列
    const QUEUE_EMAIL_ERROR_NOTIFY = 'sms_email_error_notify'; //错误通知邮件

    //路由
    const ROUTE_KEY_EMAIL_ERROR_NOTIFY_1 = 'sms.email.error_notify1'; //错误通知邮件路由1

    private static $_instance = null;

    /** @var AMQPStreamConnection $_connection*/
    private $_connection = null;

    /**
     * QueueHelper constructor.
     * @throws \Exception
     */
    private function __construct()
    {
        $mqCluster = Config::instance()->getParams('rabbitmq-cluster');
        $this->_connection = AMQPStreamConnection::create_connection($mqCluster);
    }

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->_connection, $method], $arguments);
    }

    /**
     * 获得channel
     * @return AMQPChannel
     */
    public function getChannel() : AMQPChannel
    {
        return $this->_connection->channel();
    }

    /**
     * @throws \Exception
     */
    public function close()
    {
        $this->_connection->close();
    }
}