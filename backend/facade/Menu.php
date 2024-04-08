<?php

namespace backend\facade;

use yii\base\Model;
use backend\models\BackendMenuNode;
use yii\base\UserException;
use common\helper\ArrayHelper;


class Menu extends Model
{
    // 类型
    private static $param_type = [
        1, // 目录
        2, // 页面
        3, // 按钮
    ];

    // 是否隐藏
    private static $param_is_hide = [
        0, // 否
        1, // 是
    ];

    // 是否公共
    private static $param_is_public = [
        0, // 否
        1, // 是
    ];

    // 是否父菜单
    private static $param_is_parent = [
        -1, // 全部
        0, // 否
        1, // 是
    ];

    // 状态
    private static $param_status = [
        -1, // 全部
        0, // 禁用
        1, // 正常
    ];

    public static function getAll()
    {
        return BackendMenuNode::find()
            ->select(['id', 'name'])
            ->where(['is_deleted' => 0, 'is_public' => 0])
            ->asArray()
            ->all();
    }

    public static function getMenuFullName($menuId)
    {
        $menu = BackendMenuNode::findOne($menuId);
        if (!$menu) {
            return '无';
        }
        if ($menu->parent_id) {
            return self::getMenuFullName($menu->parent_id) . '/' . $menu->name;
        }
        return $menu->name;
    }

    public static function getNodeTree($includePublic = false)
    {
        $select = ['id', 'type', 'name', 'parent_id', 'is_hide'];
        if ($includePublic) {
            $select[] = 'is_public';
        }
        $query = BackendMenuNode::find()
            ->select($select)
            ->andWhere(['is_deleted' => 0]);
        if (!$includePublic) {
            $query->andWhere(['is_public' => 0]);
        }
        $list = $query->asArray()
            ->orderBy(['order' => SORT_ASC])
            ->all();


        $formedList = array_column($list, null, "id");
        return ArrayHelper::generateTree($formedList, "id", "parent_id");
    }


    private static function checkParamForType(&$var)
    {
        $var['type'] = intval($var['type']);
        if (!in_array($var['type'], static::$param_type)) {
            throw new UserException('参数[type]的值错误');
        }
    }

    private static function checkParamForRoute(&$var)
    {
        $type = $var['type'];
        $route = $var['route'];
        if (2==$type && !$route) {
            throw new UserException('参数[route]不能为空');
        }
        else if (3==$type) {
            // 接口取

            $buttonRes = Dict::getDataBySign('button');
            $buttonArr = array_column($buttonRes, 'sign');
            if(!in_array($route, $buttonArr))
            {
                throw new UserException('参数[route]值错误');
            }
        }
        else{ // 1==$type 目录

        }
    }

    private static function checkParamForIcon(&$var)
    {
        $iconRes = Dict::getDataBySign('icon');
        $iconArr = array_column($iconRes, 'sign');
        $icon = $var['icon'];
        if(!in_array($icon, $iconArr))
        {
            throw new UserException('参数[icon]值错误');
        }
    }

    private static function checkParamForHide(&$var, $pArr=[])
    {
        $pArr = array_merge(static::$param_is_hide, $pArr);

//        $var['is_hide'] = intval($var['is_hide']);
        if (!in_array($var['is_hide'], $pArr)) {
            throw new UserException('参数[is_hide]的值错误');
        }
    }

    private static function checkParamForPublic(&$var, $pArr=[])
    {
        $pArr = array_merge(static::$param_is_public, $pArr);

//        $var['is_public'] = intval($var['is_public']);
        if(!in_array($var['is_public'], $pArr)) {
            throw new UserException('参数[is_public]的值错误');
        }
    }

    private static function checkParamForParent(&$var)
    {
//        $var['is_parent'] = intval($var['is_parent']);
        if(!in_array($var['is_parent'], static::$param_is_parent)) {
            throw new UserException('参数[is_parent]的值错误');
        }
    }

    private static function checkParamForStatus(&$var, $pArr=[])
    {
        $pArr = array_merge(static::$param_status, $pArr);

//        $var['status'] = intval($var['status']);
        if(!in_array($var['status'], $pArr)) {
            throw new UserException('参数[status]的值错误');
        }
    }

    public static function search($params)
    {
        static::checkParamForParent($params);
        static::checkParamForPublic($params, [-1]);
        static::checkParamForHide($params, [-1]);
        static::checkParamForStatus($params, [-1]);

        $page = intval($params['page']);
        $page_size = intval($params['page_size']);

        $offset = max(0, $page - 1) * $page_size;
        $query = BackendMenuNode::find();

        # 菜单名称做模糊搜索
        if (!empty($params['name'])) {
            $query->andWhere(['like', 't.name', "{$params['name']}"]);
        }

        if (-1!=$params['is_parent']) {
            if (1==$params['is_parent']) {
                $query->andWhere(['=', 't.parent_id', 0]);
            }
            else
            {
                $query->andWhere(['>', 't.parent_id', 0]);
            }
        }

        if (-1!=$params['is_public']) {
            $query->andWhere(['=', 't.is_public', $params['is_public']]);
        }
        if (-1!=$params['is_hide']) {
            $query->andWhere(['=', 't.is_hide', $params['is_hide']]);
        }
        if (-1!=$params['status']) {
            $status = $params['status']? 0 : 1;
            $query->andWhere(['=', 't.is_deleted', $status]);
        }

        $list = $query
            ->alias('t')
            ->select(['t.*', "ifnull(b.name, '--') as parent_name"])
            ->leftJoin(\backend\models\BackendMenuNode::tableName() . ' as b', 'b.id=t.parent_id')
            ->orderBy(['t.id' => SORT_DESC])
            ->offset($offset)
            ->limit($page_size)
            ->asArray()
            ->all();
        return [
            'total' => $query->count(),
            'list' => $list,
        ];
    }



    public static function add($params)
    {
        static::checkParamForHide($params);
        static::checkParamForPublic($params);
        static::checkParamForType($params);
        static::checkParamForRoute($params);
        static::checkParamForIcon($params);


        //检查父ID是否存在
        if ($params['parent_id']
            && !BackendMenuNode::find()->where(['id' => $params['parent_id'],])->exists()) {
            throw new UserException('父ID不存在，或已禁用');
        }

        $m = new BackendMenuNode(ArrayHelper::cp($params, [
            'name',
            'type',
            'is_hide',
            'is_public',
            'parent_id',
            'order',
            'route',
            'icon',
        ]));
        $m->operator_id = User::getUser()->id;
        $m->is_deleted = $params['status'] ? 0 : 1;

        $m->msave();

        return $m;
    }

    public static function edit($params)
    {
        static::checkParamForHide($params);
        static::checkParamForPublic($params);
        static::checkParamForType($params);
        static::checkParamForRoute($params);
        static::checkParamForIcon($params);

        //检查父ID是否存在
        if ($params['parent_id'] &&
            !BackendMenuNode::find()->where(['id' => $params['parent_id'],])->exists()) {
            throw new UserException('父ID不存在，或已禁用');
        }

        $m = BackendMenuNode::findOne($params['id']);
        if (!$m) {
            throw new UserException('菜单不存在');
        }

        $m->setAttributes(ArrayHelper::cp($params, [
            'name',
            'type',
            'is_hide',
            'is_public',
            'parent_id',
            'order',
            'route',
            'icon',
        ]));
        $m->operator_id = User::getUser()->id;
        $m->msave();
        return $m;
    }

    public static function changeStatus($params)
    {
        $m = BackendMenuNode::findOne($params['id']);
        if (!$m) {
            throw new UserException('菜单不存在');
        }

        $m->is_deleted = $params['status'] ? 0 : 1;
        $m->operator_id = User::getUser()->id;
        $m->msave();
        return $m;
    }



}