<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/6/13
 * Time: 5:06 PM
 */

namespace app\modules\user\tables;

class User extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'User';
    }
}