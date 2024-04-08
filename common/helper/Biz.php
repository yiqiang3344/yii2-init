<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/11
 * Time: 11:33 AM
 */

namespace common\helper;


class Biz
{
    /**
     * 获取固定格式异常信息
     * @param \Exception $e
     * @return string
     */
    public static function exceptionInfo(\Exception $e)
    {
        return $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    }
}