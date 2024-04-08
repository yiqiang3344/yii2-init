<?php

namespace backend\models;

use backend\facade\Login;
use backend\facade\Menu;
use backend\helper\JwtHelper;
use backend\tables\BackendUser;
use common\helper\ArrayHelper;
use common\helper\Env;
use common\models\MActiveRecord;
use yiqiang3344\yii2_lib\helper\Time;
use yii\web\IdentityInterface;
use \Yii;

class User extends BackendUser implements IdentityInterface
{
    use MActiveRecord;

    const TYPE_MOBILE = 1;
    const TYPE_JWT = 2;

    public function generateToken()
    {
        \backend\facade\User::setNoActionLoginExpire($this);
        \backend\facade\User::setNoActionUserExpire($this);
        JwtHelper::$expireTime = Login::getLoginExpireByType('has_action');
        return JwtHelper::generateToken($this->id, 'backend');
    }

    public static function generatePassword($password)
    {
        return Yii::$app->security->generatePasswordHash($password, 4);
    }

    public function isPasswordExpire()
    {
        return $this->password_expire != 0 && Time::time() > $this->password_expire;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function getInfo()
    {
        return ArrayHelper::cp($this->toArray(), [
            'id',
            'name',
            'mobile',
        ]);
    }

    public function getMenus()
    {
        $tree = Menu::getNodeTree(true);
        $map = BackendMenuNode::find()
            ->alias('t')
            ->select([
                't.id',
            ])
            ->innerJoin(BackendUserRole::tableName() . ' as b', 'b.user_id=:user_id and b.is_deleted=0', [
                ':user_id' => $this->id,
            ])
            ->innerJoin(BackendRoleNode::tableName() . ' as c', 't.id=c.node_id and c.role_id=b.role_id and c.type=:type and c.is_deleted=0', [
                ':type' => BackendRoleNode::TYPE_MENU,
            ])
            ->where(['t.is_deleted' => 0])
            ->asArray()
            ->indexBy('id')
            ->all();
        ArrayHelper::handleSelectTree($tree, $map);
        ArrayHelper::removeUnSelectTree($tree);
        return $tree;
    }

    public function getRoles()
    {
        return self::find()
            ->alias('t')
            ->select([
                'c.id',
                'c.name',
                'c.comment',
            ])
            ->innerJoin(BackendUserRole::tableName() . ' as b', 'b.user_id=t.id and b.is_deleted=0')
            ->innerJoin(BackendRole::tableName() . ' as c', 'c.id=b.role_id and c.is_deleted=0')
            ->where(['t.id' => $this->id])
            ->asArray()
            ->all();
    }

    public function bindRole($roleId)
    {
        $m = BackendUserRole::findOne(['user_id' => $this->id, 'role_id' => $roleId]);
        if (!$m) {
            $m = new BackendUserRole([
                'user_id' => $this->id,
                'role_id' => $roleId,
            ]);
        }
        $m->operator_id = \backend\facade\User::getCurrentUserId();
        $m->is_deleted = 0;
        $m->msave();
    }

    public function changePassword($password)
    {
        $this->password = self::generatePassword($password);
        $this->password_expire = Login::getPwdExpireTime();
        $this->msave();

        //清除登录token
        JwtHelper::clearByUID($this->id);
    }

    public function invalid()
    {
        $this->is_deleted = 1;
        $this->operator_id = 0; //系统默认操作
        $this->msave();

        //清除登录token
        JwtHelper::clearByUID($this->id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    /**
     * {@inheritdoc}
     * @return User
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if ($type == self::TYPE_MOBILE) {
            $user = self::findOne(['mobile' => $token, 'is_deleted' => 0]);
        } elseif ($type = self::TYPE_JWT) {
            $data = JwtHelper::validateToken($token);
            if (empty($data['uid'])) {
                return null;
            }
            $user = self::findOne(['id' => $data['uid'], 'is_deleted' => 0]);
        } else {
            $user = null;
        }
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