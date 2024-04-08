<?php

namespace backend\facade;


use backend\models\BackendInterfaceNode;
use backend\models\BackendRoleNode;
use backend\models\BackendSystemConfig;
use backend\models\BackendUserRole;
use common\helper\ArrayHelper;
use common\helper\redis\Redis;
use Yii;
use yii\base\UserException;

class User
{
    const USER_EXPIRE_HASH = [
        '周' => 86400 * 7,
        '月' => 86400 * 30,
    ];

    /**
     * @return \backend\models\User
     * @throws UserException
     */
    public static function getUser()
    {
        if (Yii::$app->id == 'console') {
            return null;
        }
        if (Yii::$app->user->isGuest) {
            throw new UserException('', 200000);
        }
        /** @var \backend\models\User $user */
        $user = Yii::$app->user->identity;
        return $user;
    }

    public static function getCurrentUserId()
    {
        $user = self::getUser();
        return $user ? $user->id : 0;
    }

    /**
     * 获取用户角色对应接口权限列表
     */
    public static function getUserInterfaceNodes(\backend\models\User $user)
    {
        $nodeMap1 = \backend\models\User::find()
            ->alias('t')
            ->select([
                'd.sign',
                'd.is_public',
            ])
            ->innerJoin(BackendUserRole::tableName() . ' as b', 'b.user_id=t.id and b.is_deleted=0')
            ->innerJoin(BackendRoleNode::tableName() . ' as c', 'c.role_id=b.role_id and c.is_deleted=0')
            ->innerJoin(BackendInterfaceNode::tableName() . ' as d', 'd.id=c.node_id and d.is_deleted=0')
            ->where(['t.id' => $user->id, 'd.type' => InterfaceNode::TYPE_ACTION])
            ->asArray()
            ->indexBy('sign')
            ->all();

        //加入公共接口
        $nodeMap2 = InterfaceNode::getPublicNodes();

        return ArrayHelper::merge($nodeMap1, $nodeMap2);
    }

    public static function getExpire()
    {
        $name = 'UserExpireInfo';
        $m = BackendSystemConfig::findOne(['name' => $name]);
        if (!$m) {
            return [
                'num' => 2,
                'unit' => '周',
            ];
        }
        return json_decode($m->value, true);
    }

    public static function getExpireTime()
    {
        $data = self::getExpire();
        return $data['num'] * self::USER_EXPIRE_HASH[$data['unit']];
    }

    public static function setExpire($params)
    {
        //检查参数
        if ($params['num'] <= 0) {
            throw new UserException('num值不合法');
        }
        if (!isset(self::USER_EXPIRE_HASH[$params['unit']])) {
            throw new UserException('unit值不合法');
        }

        $name = 'UserExpireInfo';
        $m = BackendSystemConfig::findOne(['name' => $name]);
        if (!$m) {
            $m = new BackendSystemConfig([
                'name' => $name,
            ]);
        }
        $m->value = json_encode(ArrayHelper::cp($params, ['num', 'unit']), JSON_UNESCAPED_UNICODE);
        $m->operator_id = User::getUser()->operator_id;
        $m->msave();
        return true;
    }

    public static function search($params)
    {
        $offset = max(0, $params['page'] - 1) * $params['page_size'];
        $query = \backend\models\User::find();
        if (!empty($params['name'])) {
            $query->andWhere(['=', 't.name', $params['name']]);
        }
        if (!empty($params['role_id'])) {
            $query->andWhere(['=', 'c.role_id', intval($params['role_id'])]);
            $query->innerJoin(BackendUserRole::tableName() . ' as c', 'c.user_id=t.id and c.is_deleted=0');
        }
        if ($params['status'] != -1) {
            $query->andWhere(['=', 't.is_deleted', $params['status'] ? 0 : 1]);
        }
        $total = $query->count();
        /** @var \backend\models\User[] $_list */
        $_list = $query
            ->alias('t')
            ->leftJoin(\backend\models\User::tableName() . ' as b', 'b.id=t.operator_id')
            ->orderBy(['t.id' => SORT_DESC])
            ->offset($offset)
            ->limit($params['page_size'])
            ->all();

        $operatorMap = \backend\models\User::find()
            ->select(['id', 'name'])
            ->where([
                'id' => array_column(ArrayHelper::toArray($_list), 'id'),
                'is_deleted' => 0,
            ])->asArray()
            ->indexBy('id')
            ->all();

        $list = [];
        foreach ($_list as $_user) {
            $_d = ArrayHelper::cp($_user, ['id', 'name', 'is_deleted', 'created_time']);
            $_d['operator_name'] = isset($operatorMap[$_user->operator_id]) ? $operatorMap[$_user->operator_id]['name'] : '系统';
            $_d['roles'] = $_user->getRoles();
            $list[] = $_d;
        }

        return [
            'total' => $total,
            'list' => $list,
        ];
    }

    public static function add($params)
    {
        //检查角色是否存在
        $roleMap = ArrayHelper::listMap(Role::getAll(), 'id');
        foreach ($params['roles'] as $_id) {
            if (!isset($roleMap[$_id])) {
                throw new UserException('角色不存在:' . $_id);
            }
        }

        $m = new \backend\models\User(ArrayHelper::cp($params, [
            'name',
            'mobile',
        ]));
        $m->password = \backend\models\User::generatePassword(Login::getInitPwd());
        $m->password_expire = Login::getPwdExpireTime();
        $m->is_deleted = $params['status'] ? 0 : 1;
        $m->operator_id = User::getCurrentUserId();
        $m->msave();

        //绑定角色
        foreach ($params['roles'] as $roleId) {
            $m->bindRole($roleId);
        }

        return $m;
    }

    public static function edit($params)
    {
        //检查角色是否存在
        $roleMap = ArrayHelper::listMap(Role::getAll(), 'id');
        foreach ($params['roles'] as $_id) {
            if (!isset($roleMap[$_id])) {
                throw new UserException('角色不存在:' . $_id);
            }
        }

        $m = \backend\models\User::findOne($params['id']);
        if (!$m) {
            throw new UserException('账户不存在');
        }

        $m->setAttributes(ArrayHelper::cp($params, [
            'name',
            'mobile',
        ]));
        $m->operator_id = User::getCurrentUserId();
        $m->msave();

        //绑定角色
        foreach ($params['roles'] as $roleId) {
            $m->bindRole($roleId);
        }

        return $m;
    }

    public static function changeStatus($params)
    {
        $m = \backend\models\User::findOne($params['id']);
        if (!$m) {
            throw new UserException('账户不存在');
        }

        $m->is_deleted = $params['status'] ? 0 : 1;
        $m->operator_id = User::getCurrentUserId();
        $m->msave();
        return $m;
    }

    public static function initPwd($params)
    {
        $m = \backend\models\User::findOne($params['id']);
        if (!$m) {
            throw new UserException('账户不存在');
        }

        $m->changePassword(Login::getInitPwd());
        return $m;
    }

    public static function setNoActionLoginExpire(\backend\models\User $user)
    {
        $key = Redis::generateKey('userLoginNoActionExpire', [$user->id]);
        Redis::instance()->setex($key, Login::getLoginExpireByType('no_action'), 1);
        return true;
    }

    public static function checkNoActionLoginExpire(\backend\models\User $user)
    {
        $key = Redis::generateKey('userLoginNoActionExpire', [$user->id]);
        if (!Redis::instance()->get($key)) {
            throw new UserException('长时间无操作', 200000);
        }
        return true;
    }

    public static function setNoActionUserExpire(\backend\models\User $user)
    {
        $key = Redis::generateKey('userStatusNoActionExpire', [$user->id]);
        Redis::instance()->setex($key, self::getExpireTime(), 1);
        return true;
    }

    public static function checkNoActionUserExpire(\backend\models\User $user)
    {
        //超级管理员除外
        $map = ArrayHelper::listMap($user->getRoles(), 'name');
        if (isset($map['超级管理员'])) {
            return true;
        }

        $key = Redis::generateKey('userStatusNoActionExpire', [$user->id]);
        if (!Redis::instance()->get($key)) {
            $user->invalid(); //账号禁用
            throw new UserException('长时间无操作', 200030);
        }
        return true;
    }
}