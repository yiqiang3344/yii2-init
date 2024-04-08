<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/12
 * Time: 8:12 PM
 */

namespace console\controllers;


use common\facade\Config;
use common\helper\Env;
use yiqiang3344\yii2_lib\helper\Time;
use console\logging\ConsoleLog;
use yii\console\Controller;

/**
 * 脚本父类
 */
class BaseController extends Controller
{
    protected $access_log = [];

    public function addRequestLog()
    {
        $this->addLog('request', ['url' => Env::getUri(), 'params' => $_SERVER['argv']]);
    }

    public function addLog($type, $logContent)
    {
        $this->access_log[$type] = [
            'time' => Time::nowWithMicros(),
            'info' => $logContent,
        ];
        return $this;
    }

    /**
     * @return $this
     * @throws \yii\base\Exception
     */
    public function logFlush()
    {
        if (empty($this->access_log)) {
            return $this;
        }
        if (Config::instance()->getConsoleAccessLogSwitch()) {
            //如果没有request日志，说明是架构级的错误，则补上请求日志
            if (empty($this->access_log['request'])) {
                $this->addRequestLog();
            }
            ConsoleLog::log($this->access_log);
        }
        $this->access_log = [];
        return $this;
    }

    /**
     * @throws \yii\base\Exception
     */
    public function __destruct()
    {
        $this->logFlush();
    }

    /**
     * @param $action
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeAction($action)
    {
        $this->addRequestLog();
        //常驻进程的话，直接写执行日志，如果有报错，后面会再补一条错误日志，他们的request_float_number是一致的
        if ($this->hasMethod('termHandle')) {
            $this->logFlush();
        }
        return parent::beforeAction($action);
    }

    /**
     * @param string $id
     * @param array $params
     * @return mixed
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    public function runAction($id, $params = [])
    {
        try {
            return parent::runAction($id, $params);
        } catch (\Throwable $e) {
            $this->addLog('error', '[code ' . $e->getCode() . ']' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . ' Stack trace:' . PHP_EOL . $e->getTraceAsString());
            $this->logFlush();
            throw $e;
        }
    }

    /**
     * 输出方法
     * @param $message
     */
    protected function output($message)
    {
        echo date('Y-m-d H:i:s') . ' ' . htmlspecialchars($message) . PHP_EOL;
    }
}