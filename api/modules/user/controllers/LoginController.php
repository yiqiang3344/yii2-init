<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/6/11
 * Time: 2:36 PM
 */

namespace api\modules\user\controllers;

use api\modules\user\facade\User;
use common\facade\Sms;
use common\helper\JwtHelper;
use common\helper\RequestHelper;
use common\helper\Validator;
use Yii;

class LoginController extends BaseController
{
    /**
     * @throws \Exception
     */
    public function actionIndex()
    {
        $params = Yii::$app->request->getBodyParams();
        Validator::checkParams($params, [
            'mobile' => ['name' => '手机号', 'type' => 'mobile'],
            'captcha' => ['name' => '验证码', 'type' => 'string'],
        ]);
        $mobile = $params['mobile'];
        $captcha = $params['captcha'];

        $requestHelper = RequestHelper::instance();
        Sms::verify($mobile, $captcha, $requestHelper->appName);

        $userInfo = User::instance()->loginOrRegister($mobile, $requestHelper->appName);

        $token = JwtHelper::generateToken($userInfo['mobile'], $requestHelper->appName);

        $data = ['mobile' => $userInfo['mobile'], 'token' => $token];

        $this->success($data);
    }
}