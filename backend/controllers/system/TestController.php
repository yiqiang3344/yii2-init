<?php

namespace backend\controllers\system;

use backend\controllers\AuthBaseController;
use common\helper\Validator;

/**
 * @test
 */
class TestController extends AuthBaseController
{
    public function actionTest()
    {
        $params = $this->request->getBodyParam('args');

        Validator::checkParams($params, [
            'test' => ['name' => '测试', 'type' => 'string']
        ]);

        return $this->success($params);
    }
}