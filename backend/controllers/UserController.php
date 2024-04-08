<?php

namespace backend\controllers;

use backend\facade\User;
use common\helper\Validator;

/**
 * 用户个人信息
 *
 * @public
 */
class UserController extends AuthBaseController
{
    /**
     * 登录检查
     */
    public function actionCheckLogin()
    {
        return $this->success();
    }

    /**
     * 获取个人用户信息
     */
    public function actionGetInfo()
    {
        $user = User::getUser();
        return $this->success([
            'info' => $user->getInfo(),
            'roles' => $user->getRoles(),
        ]);
    }

    /**
     * 修改密码
     */
    public function actionChangePassword()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'password' => ['name' => '密码', 'type' => 'string']
        ]);

        //TODO 密码强度校验

        $user = User::getUser();
        $user->changePassword($params['password']);
        return $this->success();
    }
}