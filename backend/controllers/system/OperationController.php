<?php

namespace backend\controllers\system;

use backend\controllers\AuthBaseController;
use backend\facade\Operation;
use common\helper\Validator;

/**
 * 操作记录
 */
class OperationController extends AuthBaseController
{
    /**
     * 获取操作记录列表
     */
    public function actionList()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'start_time' => ['name' => '起始时间', 'type' => 'string', 'default' => ''],
            'end_time' => ['name' => '截止时间', 'type' => 'string', 'default' => ''],
            'ip' => ['name' => 'ip', 'type' => 'string', 'default' => ''],
            'operator_name' => ['name' => '操作人姓名', 'type' => 'string', 'default' => ''],
            'operator_id' => ['name' => '操作人ID', 'type' => 'string', 'default' => ''],
            'interface_name' => ['name' => '接口名称', 'type' => 'string', 'default' => ''],
            'interface_sign' => ['name' => '接口标识', 'type' => 'string', 'default' => ''],
            'page' => ['name' => '第几页', 'type' => 'string', 'default' => 1],
            'page_size' => ['name' => '每页数据量', 'type' => 'string', 'default' => 10],
        ]);

        return $this->success(Operation::search($params));
    }
}