<?php

namespace backend\controllers\layout;

use backend\controllers\PublicBaseController;
use backend\facade\Dict;
use common\helper\Validator;

/**
 * 布局
 *
 * @public
 */
class LayoutController extends PublicBaseController
{
    /**
     * 获取系统字典信息
     */
    public function actionGetDictBySign()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'sign' => ['name' => '标识', 'type' => 'string'],
        ]);

        return $this->success([
            'data' => Dict::getDataBySign($params['sign']),
        ]);
    }
}