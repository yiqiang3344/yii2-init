<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/8/23
 * Time: 12:06 PM
 */

namespace common\models;

use common\logging\ILogObject;
use yii\web\IdentityInterface;

/**
 * Class User
 * @package common\models
 * @property int $id
 * @property string $mobile
 * @property string $user_name
 * @property string $email
 */
class User extends BaseData implements IdentityInterface, ILogObject
{
    /** @var User $user */
    public static $user = null;

    public function getLogExtraParams()
    {
        return [
            'mobile' => $this->mobile,
            'user_name' => $this->user_name,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        $user = self::$user;
        return $user && $user->id == $id ? $user : null;
    }

    /**
     * {@inheritdoc}
     * @return User
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = null;
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return true;
    }
}