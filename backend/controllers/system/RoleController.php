<?php

namespace backend\controllers\system;

use backend\controllers\AuthBaseController;
use backend\facade\Role;
use common\helper\Validator;

/**
 * 角色管理
 */
class RoleController extends AuthBaseController
{
    /**
     * 获取角色列表
     */
    public function actionList()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'name' => ['name' => '名称', 'type' => 'string', 'default' => ''],
            'status' => ['name' => '状态', 'type' => 'string', 'default' => -1],
            'page' => ['name' => '第几页', 'type' => 'string', 'default' => 1],
            'page_size' => ['name' => '每页数据量', 'type' => 'string', 'default' => 10],
        ]);

        return $this->success(Role::search($params));
    }

    /**
     * 添加角色
     *
     * @sendNotify
     */
    public function actionAdd()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'name' => ['name' => '名称', 'type' => 'string'],
            'comment' => ['name' => '描述', 'type' => 'string', 'default' => ''],
            'status' => ['name' => '状态', 'type' => 'string', 'default' => 0],
            'menus' => ['name' => '菜单列表', 'type' => 'array', 'default' => []],
            'interfaces' => ['name' => '接口列表', 'type' => 'array', 'default' => []],
        ]);
        Role::add($params);
        return $this->success();
    }

    /**
     * 获取角色信息
     */
    public function actionGetInfo()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'id' => ['name' => 'id', 'type' => 'string'],
        ]);
        return $this->success(Role::getInfo($params['id']));
    }

    /**
     * 编辑角色
     *
     * @sendNotify
     */
    public function actionEdit()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'id' => ['name' => 'id', 'type' => 'string'],
            'name' => ['name' => '名称', 'type' => 'string'],
            'comment' => ['name' => '描述', 'type' => 'string', 'default' => ''],
            'status' => ['name' => '状态', 'type' => 'string', 'default' => 0],
            'menus' => ['name' => '菜单列表', 'type' => 'array', 'default' => []],
            'interfaces' => ['name' => '接口列表', 'type' => 'array', 'default' => []],
        ]);
        Role::edit($params);
        return $this->success();
    }

    /**
     * 修改角色状态
     *
     * @sendNotify
     */
    public function actionChangeStatus()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'id' => ['name' => 'id', 'type' => 'string'],
            'status' => ['name' => '状态', 'type' => 'string', 'default' => 0],
        ]);
        Role::changeStatus($params);
        return $this->success();
    }

    /**
     * 获取权限列表
     */
    public function actionNodeList()
    {
        return $this->success(Role::getNodes());
    }
}