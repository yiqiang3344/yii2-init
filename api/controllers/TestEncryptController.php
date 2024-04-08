<?php

namespace api\controllers;

use common\helper\Env;
use common\helper\Validator;

/**
 * 需要加密的请求用例
 */
class TestEncryptController extends BaseEncryptController
{
    /**
     * 加密接口测试用例
     * @return \yii\web\Response
     * @throws \yiqiang3344\yii2_lib\helper\exception\ParamsInvalidException
     * @throws \yii\base\Exception
     */
    public function actionTest()
    {
        $originParams = $this->request->getBodyParam('args');
        $params = Env::getArgs();

        Validator::checkParams($params, [
            'test' => ['name' => '测试', 'type' => 'string']
        ]);

        return $this->success([
            'args' => $params,
            'origin_args' => $originParams,
        ]);
    }
}