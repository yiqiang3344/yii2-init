<?php

namespace backend\controllers\layout;

use backend\controllers\PublicBaseController;
use backend\facade\Login;
use common\helper\Validator;

/**
 * 公共登录
 *
 * @public
 */
class LoginController extends PublicBaseController
{
    /**
     * 获取登录方式列表
     */
    public function actionGetLoginMethods()
    {
        return $this->success([
            'list' => Login::getLoginMethods(),
        ]);
    }

    /**
     * 密码登录
     */
    public function actionByPassword()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'mobile' => ['name' => '手机号', 'type' => 'mobile'],
            'password' => ['name' => '密码', 'type' => 'string'],
        ]);

        return $this->success(Login::loginByPassword($params['mobile'], $params['password']));
    }

    /**
     * 发送短信验证码
     */
    public function actionSendCaptcha()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'mobile' => ['name' => '手机号', 'type' => 'mobile'],
        ]);

        Login::sendCaptcha($params['mobile']);
        return $this->success();
    }

    /**
     * 短信验证码登录
     */
    public function actionByCaptcha()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'mobile' => ['name' => '手机号', 'type' => 'mobile'],
            'captcha' => ['name' => '验证码', 'type' => 'string'],
        ]);

        return $this->success(Login::loginByCaptcha($params['mobile'], $params['captcha']));
    }

    /**
     * 钉钉扫码登录
     */
    public function actionByDingding()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'code' => ['name' => '代码', 'type' => 'string'],
        ]);

        return $this->success(Login::loginByDingding($params['code']));
    }
}