<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/11
 * Time: 11:22 AM
 */

namespace common\logging;


/**
 * 日志对象接口
 * Class AdminTraceLog
 * @package common\logging
 */
interface ILogObject
{
    /**
     * 获取日志额外参数
     * @return array
     */
    public function getLogExtraParams();
}