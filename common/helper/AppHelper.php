<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/6/20
 * Time: 11:51 AM
 */

namespace common\helper;

use yii\base\Model;

class AppHelper extends Model
{
    /**
     * 通过 $appName 获取对应的表名
     * @param $appName
     * @return string
     * @throws \Exception
     */
    public static function getUserTableByAppName($appName)
    {
        switch ($appName)
        {
            case 'hb' :
                $userTable = 'credit_user';
                break;
            default :
                throw new \Exception("{$appName} : 没有对应的 user table");
        }

        return $userTable;
    }
}