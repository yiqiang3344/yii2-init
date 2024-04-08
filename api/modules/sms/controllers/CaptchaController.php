<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/6/12
 * Time: 2:50 PM
 */

namespace api\modules\sms\controllers;

use api\controllers\BaseController;
use common\facade\Sms;
use common\helper\RequestHelper;
use common\helper\Validator;
use Yii;

class CaptchaController extends BaseController
{
    /**
     * @throws \Exception
     */
    public function actionGet()
    {
        $params = Yii::$app->request->getBodyParams();
        Validator::checkParams($params, [
            'mobile' => ['name' => '手机号', 'type' => 'mobile'],
        ]);
        $mobile = $params['mobile'];

        Sms::sendCaptchaCode($mobile, RequestHelper::instance()->appName);

        $this->success();
    }
}