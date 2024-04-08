<?php

namespace api\controllers;

use common\helper\Validator;

/**
 * 无需加密的请求用例
 */
class TestController extends BaseController
{
    /**
     * 标准化测试接口，上线后可删除
     * @return \yii\web\Response
     * @throws \yiqiang3344\yii2_lib\helper\exception\ParamsInvalidException
     * @throws \yii\base\Exception
     */
    public function actionTest()
    {
        $params = $this->request->getBodyParam('args');

        Validator::checkParams($params, [
            'test' => ['name' => '测试', 'type' => 'string']
        ]);

        return $this->success($params);
    }
}