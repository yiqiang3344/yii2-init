<?php

namespace backend\models;

use backend\facade\InterfaceNode;
use backend\facade\Menu;
use common\helper\ArrayHelper;
use common\models\MActiveRecord;

class BackendRole extends \backend\tables\BackendRole
{
    use MActiveRecord;

    public function bindMenus($menus)
    {
        $operatorId = \backend\facade\User::getCurrentUserId();
        //所有权限全部清除
        BackendRoleNode::updateAll(['is_deleted' => 1, 'operator_id' => $operatorId], [
            'type' => BackendRoleNode::TYPE_MENU,
            'role_id' => $this->id,
        ]);

        foreach ($menus as $menu) {
            $m = BackendRoleNode::findOne(['type' => BackendRoleNode::TYPE_MENU, 'role_id' => $this->id, 'node_id' => $menu]);
            if (!$m) {
                $m = new BackendRoleNode([
                    'type' => BackendRoleNode::TYPE_MENU,
                    'role_id' => $this->id,
                    'node_id' => $menu,
                ]);
            }
            $m->operator_id = $operatorId;
            $m->is_deleted = 0;
            $m->msave();
        }
        return true;
    }

    public function bindInterfaces($interfaces)
    {
        $operatorId = \backend\facade\User::getCurrentUserId();
        //所有权限全部清除
        BackendRoleNode::updateAll(['is_deleted' => 1, 'operator_id' => $operatorId], [
            'type' => BackendRoleNode::TYPE_INTERFACE,
            'role_id' => $this->id,
        ]);

        //重新配置权限
        foreach ($interfaces as $interface) {
            $m = BackendRoleNode::findOne(['type' => BackendRoleNode::TYPE_INTERFACE, 'role_id' => $this->id, 'node_id' => $interface]);
            if (!$m) {
                $m = new BackendRoleNode([
                    'type' => BackendRoleNode::TYPE_INTERFACE,
                    'role_id' => $this->id,
                    'node_id' => $interface,
                ]);
            }
            $m->operator_id = $operatorId;
            $m->is_deleted = 0;
            $m->msave();
        }
        return true;
    }

    public function getMenuTree()
    {
        $allTree = Menu::getNodeTree();
        $roleNoteMap = BackendMenuNode::find()
            ->select(['t.id'])
            ->alias('t')
            ->innerJoin(BackendRoleNode::tableName() . ' as b', 'b.node_id=t.id and b.is_deleted=0')
            ->where([
                't.is_deleted' => 0,
                'b.type' => BackendRoleNode::TYPE_MENU,
                'b.role_id' => $this->id,
            ])
            ->asArray()
            ->indexBy('id')
            ->all();
        ArrayHelper::handleSelectTree($allTree, $roleNoteMap);
        return $allTree;
    }

    public function getInterfaceTree()
    {
        $allTree = InterfaceNode::getNodeTree();
        $roleNoteMap = BackendInterfaceNode::find()
            ->select(['t.id'])
            ->alias('t')
            ->innerJoin(BackendRoleNode::tableName() . ' as b', 'b.node_id=t.id and b.is_deleted=0')
            ->where([
                't.is_deleted' => 0,
                'b.type' => BackendRoleNode::TYPE_INTERFACE,
                'b.role_id' => $this->id,
            ])
            ->asArray()
            ->indexBy('id')
            ->all();
        ArrayHelper::handleSelectTree($allTree, $roleNoteMap);
        return $allTree;
    }
}
