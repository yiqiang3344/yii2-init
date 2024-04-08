<?php

namespace backend\controllers\system;

use backend\controllers\AuthBaseController;
use backend\facade\Dict;
use common\helper\Validator;

/**
 * 系统字典管理
 */
class DictController extends AuthBaseController
{
    /**
     * 获取字典列表
     */
    public function actionList()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'sign' => ['name' => '标识', 'type' => 'string', 'default' => ''],
            'name' => ['name' => '名称', 'type' => 'string', 'default' => ''],
            'parent_id' => ['name' => '父ID', 'type' => 'string', 'default' => ''],
            'page' => ['name' => '第几页', 'type' => 'string', 'default' => '1'],
            'page_size' => ['name' => '每页数据量', 'type' => 'string', 'default' => '10'],
        ]);

        return $this->success(Dict::search($params));
    }

    /**
     * 添加字典
     *
     * @sendNotify
     */
    public function actionAdd()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'sign' => ['name' => '标识', 'type' => 'string'],
            'name' => ['name' => '名称', 'type' => 'string'],
            'comment' => ['name' => '说明', 'type' => 'string'],
            'parent_id' => ['name' => '父ID', 'type' => 'string', 'default' => '0'],
            'status' => ['name' => '状态', 'type' => 'string', 'default' => '0'],
        ]);
        Dict::add($params);
        return $this->success();
    }

    /**
     * 编辑字典
     *
     * @sendNotify
     */
    public function actionEdit()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'id' => ['name' => 'id', 'type' => 'integer'],
            'sign' => ['name' => '标识', 'type' => 'string'],
            'name' => ['name' => '名称', 'type' => 'string'],
            'comment' => ['name' => '说明', 'type' => 'string'],
            'parent_id' => ['name' => '父ID', 'type' => 'string', 'default' => '0'],
        ]);
        Dict::edit($params);
        return $this->success();
    }

    /**
     * 修改字典状态
     *
     * @sendNotify
     */
    public function actionChangeStatus()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'id' => ['name' => 'id', 'type' => 'string'],
            'status' => ['name' => '状态', 'type' => 'string', 'default' => '0'],
        ]);
        Dict::changeStatus($params);
        return $this->success();
    }
}