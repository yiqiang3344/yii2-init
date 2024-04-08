<?php

namespace backend\controllers;

use backend\facade\InterfaceNode;
use common\helper\ArrayHelper;
use common\helper\Validator;

/**
 * @test
 */
class TestController extends BaseController
{
    public function actionTest()
    {
        $nodeMap = ArrayHelper::listMap(InterfaceNode::allActions(), 'sign', ['is_public']);
        var_dump($nodeMap);
        die;
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'test' => ['name' => '测试', 'type' => 'string']
        ]);

        return $this->success($params);
    }
}