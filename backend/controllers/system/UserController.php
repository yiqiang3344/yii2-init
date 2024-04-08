<?php

namespace backend\controllers\system;

use backend\controllers\AuthBaseController;
use backend\facade\Role;
use backend\facade\User;
use common\helper\Validator;

/**
 * 账户管理
 */
class UserController extends AuthBaseController
{
    /**
     * 获取账户过期时间信息
     */
    public function actionGetExpire()
    {
        return $this->success(User::getExpire());
    }

    /**
     * 设置账户过期时间
     *
     * @sendNotify
     */
    public function actionSetExpire()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'num' => ['name' => '数量', 'type' => 'string'],
            'unit' => ['name' => '单位', 'type' => 'string'],
        ]);
        User::setExpire($params);
        return $this->success();
    }

    /**
     * 获取角色列表
     */
    public function actionRoleList()
    {
        return $this->success(Role::getAll());
    }

    /**
     * 获取账户列表
     */
    public function actionList()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'name' => ['name' => '名称', 'type' => 'string', 'default' => ''],
            'role_id' => ['name' => '角色ID', 'type' => 'string', 'default' => 0],
            'status' => ['name' => '状态', 'type' => 'string', 'default' => -1],
            'page' => ['name' => '第几页', 'type' => 'string', 'default' => 1],
            'page_size' => ['name' => '每页数据量', 'type' => 'string', 'default' => 10],
        ]);

        return $this->success(User::search($params));
    }

    /**
     * 添加账户
     *
     * @sendNotify
     */
    public function actionAdd()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'name' => ['name' => '名称', 'type' => 'string'],
            'mobile' => ['name' => '手机号', 'type' => 'mobile'],
            'roles' => ['name' => '角色列表', 'type' => 'array'],
            'status' => ['name' => '状态', 'type' => 'string', 'default' => 0],
        ]);
        User::add($params);
        return $this->success();
    }

    /**
     * 编辑账户
     *
     * @sendNotify
     */
    public function actionEdit()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'id' => ['name' => 'id', 'type' => 'string'],
            'name' => ['name' => '名称', 'type' => 'string'],
            'mobile' => ['name' => '手机号', 'type' => 'mobile'],
            'roles' => ['name' => '角色列表', 'type' => 'array'],
        ]);
        User::edit($params);
        return $this->success();
    }

    /**
     * 修改账户状态
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
        User::changeStatus($params);
        return $this->success();
    }

    /**
     * 初始化账户密码
     *
     * @sendNotify
     */
    public function actionInitPwd()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'id' => ['name' => 'id', 'type' => 'string'],
        ]);
        User::initPwd($params['id']);
        return $this->success();
    }
}