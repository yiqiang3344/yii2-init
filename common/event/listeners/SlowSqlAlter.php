<?php


namespace common\event\listeners;


use common\facade\CommonSms;
use common\helper\StringHelper;
use yiqiang3344\yii2_lib\helper\DebugBacktrace;
use yiqiang3344\yii2_lib\helper\EnvV2;
use yiqiang3344\yii2_lib\helper\event\events\SlowSqlEvent;
use yiqiang3344\yii2_lib\helper\Time;
use yii\base\Event;

class SlowSqlAlter extends \yiqiang3344\yii2_lib\helper\event\listeners\SlowSqlAlter
{
    protected $alertTml = <<<TML
时间：%s
项目：%s
环境：%s
慢SQL：%s
耗时：%s秒
调用链路：%s
TML;

    public function handle(Event $event)
    {
        /** @var SlowSqlEvent $event */
        try {
            if (empty($event->slowSqlTime) || $event->cost <= $event->slowSqlTime) {
                return;
            }

            $trace = DebugBacktrace::getTraces(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15), 7, true, true);

            //记录慢sql日志，耗时可以忽略
            \Yii::warning([
                'cost' => $event->cost,
                'sql' => $event->sql,
                'trace' => $trace,
            ], 'slowSql');

            //发送钉钉预警，会有一定耗时
            if ($this->isNotify($event->sql)) {
                $env = EnvV2::isTest() ? '测试' : '生产';
                $table = StringHelper::matchTableName($event->sql);
                $subject = (!empty($table) ? '慢SQL告警(' . $table . ')' : '慢SQL告警') . Time::now();
                $content = sprintf($this->alertTml, Time::now(), PROJECT_NAME, $env . '(' . EnvV2::getEnv() . ')', $event->sql, $event->cost, print_r($trace, true));
                CommonSms::instance()->sendNotify($subject, $content, [], ['biz_architect_notify']);
            }
        } catch (\Throwable $e) {
            //异常不要抛出，不要中断正常业务，确认可以中断业务，才抛出
        }
    }

    private function isNotify($sql): bool
    {
        if (strpos($sql, 'SHOW') === 0 || strpos($sql, 'database') !== false || strpos($sql, 'information_schema') !== false) {
            return false;
        }
        return true;
    }
}