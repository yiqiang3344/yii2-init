<?php

namespace backend\controllers\system;

use backend\controllers\AuthBaseController;
use backend\facade\InterfaceNode;
use common\helper\Validator;

/**
 * 接口管理
 */
class InterfaceController extends AuthBaseController
{
    /**
     * 获取接口列表
     */
    public function actionList()
    {
        $params = $this->request->getBodyParams();

        Validator::checkParams($params, [
            'type' => ['name' => '类型', 'type' => 'string', 'default' => '-1'],
            'name' => ['name' => '名称', 'type' => 'string', 'default' => ''],
            'sign' => ['name' => '标识', 'type' => 'string', 'default' => ''],
            'is_public' => ['name' => '是否公开', 'type' => 'string', 'default' => '-1'],
            'is_deleted' => ['name' => '是否删除', 'type' => 'string', 'default' => '-1'],
            'page' => ['name' => '第几页', 'type' => 'string', 'default' => '1'],
            'page_size' => ['name' => '每页数据量', 'type' => 'string', 'default' => '10'],
        ]);

        return $this->success(InterfaceNode::search($params));
    }

    /**
     * 刷新接口列表
     *
     * @sendNotify
     */
    public function actionRefresh()
    {
        InterfaceNode::refresh();
        return $this->success();
    }
}