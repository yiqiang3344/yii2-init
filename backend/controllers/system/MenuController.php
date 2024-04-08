<?php

namespace backend\controllers\system;

use backend\controllers\AuthBaseController;
use backend\facade\Menu;
use common\helper\Validator;

/**
 * 菜单管理
 */
class MenuController extends AuthBaseController
{
    /**
     * 获取菜单列表
     * 搜索条件
     *      菜单名称（模糊匹配）
     *      是否父菜单（默认全部：-1 全部，1 是，0 否）
     *      是否公共（默认全部：-1 全部，1 是，0 否）
     *      是否隐藏（默认全部：-1 全部，1 是，0 否）
     *      状态（默认全部：-1 全部，1 正常，0 禁用）
     *
     */
    public function actionList()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'name'      => ['name' => '名称', 'type' => 'string', 'default' => ''],
            'is_parent' => ['name' => '父ID', 'type' => 'string', 'default' => -1],
            'is_public' => ['name' => '是否公共', 'type' => 'string', 'default' => -1],
            'is_hide'   => ['name' => '是否隐藏', 'type' => 'string', 'default' => -1],
            'status'    => ['name' => '状态', 'type' => 'string', 'default' => -1],
            'page'      => ['name' => '第几页', 'type' => 'string', 'default' => 1],
            'page_size' => ['name' => '每页数据量', 'type' => 'string', 'default' => 10],
        ]);

        return $this->success(Menu::search($params));
    }

    /**
     * 添加菜单
     *
     * @sendNotify
     */
    public function actionAdd()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'name'      => ['name' => '名称', 'type' => 'string'],
            'type'      => ['name' => '类型', 'type' => 'string'],
            'is_hide'   => ['name' => '是否隐藏', 'type' => 'string', 'default' => 0],
            'is_public' => ['name' => '是否公共', 'type' => 'string', 'default' => 0],
            'parent_id' => ['name' => '父ID', 'type' => 'string', 'default' => 0],
            'order'     => ['name' => '排序', 'type' => 'string', 'default' => 0],
            'route'     => ['name' => '路由', 'type' => 'string', 'default' => ''],
            'icon'      => ['name' => '图标', 'type' => 'string', 'default' => ''],
            'status'    => ['name' => '状态', 'type' => 'string', 'default' => 0],
        ]);

        Menu::add($params);
        return $this->success();
    }

    /**
     * 编辑菜单
     *
     * @sendNotify
     */
    public function actionEdit()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'id'        => ['name' => 'id', 'type' => 'string'],
            'name'      => ['name' => '名称', 'type' => 'string'],
            'type'      => ['name' => '类型', 'type' => 'string'],
            'is_hide'   => ['name' => '是否隐藏', 'type' => 'string', 'default' => 0],
            'is_public' => ['name' => '是否公共', 'type' => 'string', 'default' => 0],
            'parent_id' => ['name' => '父ID', 'type' => 'string', 'default' => 0],
            'order'     => ['name' => '排序', 'type' => 'string', 'default' => 0],
            'route'     => ['name' => '路由', 'type' => 'string', 'default' => ''],
            'icon'      => ['name' => '图标', 'type' => 'string', 'default' => ''],
        ]);
        Menu::edit($params);
        return $this->success();
    }

    /**
     * 修改菜单状态
     *
     * @sendNotify
     */
    public function actionChangeStatus()
    {
        $params = $this->request->getBodyParams();
        Validator::checkParams($params, [
            'id' => ['name' => 'id', 'type' => 'string'],
            'status' => ['name' => '状态', 'type' => 'string', 'default' => 0],
        ]);
        Menu::changeStatus($params);
        return $this->success();
    }
}