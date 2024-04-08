<?php

namespace backend\controllers\system;

use backend\controllers\AuthBaseController;
use backend\facade\Login;
use common\helper\Validator;

/**
 * 登录管理
 */
class LoginController extends AuthBaseController
{
    /**
     * 获取登录过期时间信息
     */
    public function actionGetLoginExpireInfo()
    {
        return $this->success(Login::getLoginExpireInfo());
    }

    /**
     * 设置登录过期时间
     *
     * @sendNotify
     */
    public function actionSetLoginExpireInfo()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'no_action_num' => ['name' => '无操作时数量', 'type' => 'string'],
            'no_action_unit' => ['name' => '无操作时单位', 'type' => 'string'],
            'has_action_num' => ['name' => '有操作时数量', 'type' => 'string'],
            'has_action_unit' => ['name' => '有操作时单位', 'type' => 'string'],
        ]);
        Login::setLoginExpireInfo($params);
        return $this->success();
    }

    /**
     * 获取密码有效期信息
     */
    public function actionGetPwdExpire()
    {
        return $this->success([
            'pwd_expire' => Login::getPwdExpire(),
        ]);
    }

    /**
     * 设置密码有效期
     *
     * @sendNotify
     */
    public function actionSetPwdExpire()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'expire' => ['name' => '有效期', 'type' => 'string'],
        ]);
        Login::setPwdExpire($params['expire']);
        return $this->success();
    }

    /**
     * 获取初始密码信息
     */
    public function actionGetInitPwd()
    {
        return $this->success([
            'init_pwd' => Login::getInitPwd(),
        ]);
    }

    /**
     * 设置初始密码
     *
     * @sendNotify
     */
    public function actionSetInitPwd()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'init_pwd' => ['name' => '初始密码', 'type' => 'string'],
        ]);
        Login::setInitPwd($params['init_pwd']);
        return $this->success();
    }

    /**
     * 获取登录方式信息
     */
    public function actionGetLoginMethods()
    {
        return $this->success([
            'list' => Login::getLoginMethods(),
        ]);
    }

    /**
     * 设置登录方式
     *
     * @sendNotify
     */
    public function actionSetLoginMethods()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'list' => ['name' => '登录方式列表', 'type' => 'array'],
        ]);
        Login::setLoginMethods($params['list']);
        return $this->success();
    }
}