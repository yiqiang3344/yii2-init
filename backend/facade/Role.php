<?php

namespace backend\facade;


use backend\models\BackendRole;
use common\helper\ArrayHelper;
use yii\base\UserException;

class Role
{
    public static function getAll()
    {
        return BackendRole::find()
            ->select(['id', 'name', 'comment'])
            ->where(['is_deleted' => 0])
            ->asArray()
            ->all();
    }

    public static function getNodes()
    {
        return [
            'menus' => Menu::getNodeTree(),
            'interfaces' => InterfaceNode::getNodeTree(),
        ];
    }

    public static function search($params)
    {
        $offset = max(0, $params['page'] - 1) * $params['page_size'];
        $query = BackendRole::find();
        if (!empty($params['name'])) {
            $query->andWhere(['like', 'name', "%{$params['name']}%"]);
        }
        if ($params['status'] != -1) {
            $query->andWhere(['=', 't.is_deleted', $params['status'] ? 0 : 1]);
        }
        $total = $query->count();
        $list = $query
            ->alias('t')
            ->select(['t.*', "ifnull(b.name,'系统') as operator_name"])
            ->leftJoin(\backend\models\User::tableName() . ' as b', 'b.id=t.operator_id')
            ->orderBy(['t.id' => SORT_DESC])
            ->offset($offset)
            ->limit($params['page_size'])
            ->asArray()
            ->all();
        return [
            'total' => $total,
            'list' => $list,
        ];
    }

    public static function add($params)
    {
        //菜单权限检查
        $menuMap = ArrayHelper::listMap(Menu::getAll(), 'id');
        foreach ($params['menus'] as $menu) {
            if (!isset($menuMap[$menu])) {
                throw new UserException('菜单不存在:' . $menu);
            }
        }

        //接口权限检查
        $interfaceMap = ArrayHelper::listMap(InterfaceNode::getAll(), 'id');
        foreach ($params['interfaces'] as $interface) {
            if (!isset($interfaceMap[$interface])) {
                throw new UserException('接口不存在:' . $interface);
            }
        }

        $m = new BackendRole(ArrayHelper::cp($params, [
            'name',
            'comment',
        ]));
        $m->operator_id = User::getCurrentUserId();
        $m->is_deleted = $params['status'] ? 0 : 1;
        $m->msave();

        //菜单权限设置
        $m->bindMenus($params['menus']);

        //接口权限设置
        $m->bindInterfaces($params['interfaces']);
        return $m;
    }

    public static function getInfo($id)
    {
        $m = BackendRole::findOne($id);
        if (!$m) {
            throw new UserException('角色不存在');
        }

        $data = ArrayHelper::cp($m, [
            'id',
            'name',
            'comment',
        ]);

        $data['menus'] = $m->getMenuTree();
        $data['interfaces'] = $m->getInterfaceTree();

        return $data;
    }

    public static function edit($params)
    {
        //菜单权限检查
        $menuMap = ArrayHelper::listMap(Menu::getAll(), 'id');
        foreach ($params['menus'] as $menu) {
            if (!isset($menuMap[$menu])) {
                throw new UserException('菜单不存在:' . $menu);
            }
        }

        //接口权限检查，过滤掉非方法的数据
        $interfaceMap = ArrayHelper::listMap(InterfaceNode::getAll(), 'id');
        foreach ($params['interfaces'] as $interface) {
            if (!isset($interfaceMap[$interface])) {
                throw new UserException('接口不存在:' . $interface);
            }
        }

        $m = BackendRole::findOne($params['id']);
        if (!$m) {
            throw new UserException('角色不存在');
        }

        //超级管理员角色无法修改
        if ($m->name == '超级管理员' && $m->name != $params['name']) {
            throw new UserException('此角色名不能修改');
        }

        $m->setAttributes(ArrayHelper::cp($params, [
            'name',
            'comment',
        ]));
        $m->operator_id = User::getCurrentUserId();
        $m->msave();

        //菜单权限设置
        $m->bindMenus($params['menus']);

        //接口权限设置
        $m->bindInterfaces($params['interfaces']);
        return $m;
    }

    public static function changeStatus($params)
    {
        $m = BackendRole::findOne($params['id']);
        if (!$m) {
            throw new UserException('角色不存在');
        }

        $m->is_deleted = $params['status'] ? 0 : 1;
        $m->operator_id = User::getCurrentUserId();
        $m->msave();
        return $m;
    }
}