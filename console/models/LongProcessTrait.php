<?php
/**
 * Created by PhpStorm.
 * User: sidney
 * Date: 2019/9/3
 * Time: 6:28 PM
 */

namespace console\models;

/**
 * 常驻进程处理类
 */
trait LongProcessTrait
{
    //中断信号
    protected $termFlag = false;

    /**
     * 中断信号
     */
    public function termHandle()
    {
        $this->termFlag = true;
    }

    /**
     * 常驻进程
     * @param callable $callback
     */
    protected function longProcess(callable $callback)
    {
        pcntl_async_signals(true);

        pcntl_signal(SIGTERM, [$this, 'termHandle']);

        while (true) {
            if ($this->termFlag === true) {
                return;
            }

            $callback();
        }
    }
}