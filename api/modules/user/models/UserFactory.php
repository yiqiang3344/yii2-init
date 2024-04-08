<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/6/12
 * Time: 1:35 PM
 */

namespace api\modules\user\models;

use app\modules\user\models\CreditUser;
use app\modules\user\models\User;

/**
 * Class UserFactory
 * @package api\modules\models
 */
abstract class UserFactory
{
    /**
     * @param $tableName
     * @return CreditUser|User
     * @throws \Exception
     */
    public static function create($tableName)
    {
        switch ($tableName)
        {
            case 'credit_user' :
                return new CreditUser();
                break;
            case 'User' :
                return new User();
                break;
            default :
                throw new \Exception(" 没有对应的表 {$tableName}");
        }
    }
}