<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/8/29
 * Time: 5:27 PM
 */

namespace common\models;


use yii\base\Model;

/**
 * 基础业务抽象类，需要提供API接口的Facade需要继承此类
 * User: sidney
 * Date: 2019/11/5
 * @property User $user
 */
abstract class AApi extends Model
{
    use TLog;

    public $ua;
    public $user;
    public $args;

    /**
     * 1级降级输出接口
     * @param $action
     * @param $args
     * @return mixed
     */
    abstract public function downGrade1LevelOutput($action, $args);

    /**
     * 获取业务类型，默认为类名，其他自定义可自行重载覆盖
     * @return mixed
     */
    public function getBizType()
    {
        $arr = explode('\\', static::class);
        $_type = $arr[count($arr) - 1];
        return $_type;
    }

    /**
     * 获取业务监控人员邮箱列表
     * @return array
     */
    abstract public function getMonitorAddressBook();
}