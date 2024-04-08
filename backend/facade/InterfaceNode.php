<?php

namespace backend\facade;

use backend\models\BackendInterfaceNode;
use common\helper\ArrayHelper;
use common\helper\StringHelper;
use yii\base\UserException;

class InterfaceNode
{
    const TYPE_CONTROLLER = 1;
    const TYPE_ACTION = 2;

    private static function getBaseDir()
    {
        return \Yii::getAlias('@backend') . '/controllers';
    }

    public static function getBySign($sign)
    {
        return BackendInterfaceNode::findOne(['sign' => $sign]);
    }

    public static function getAll()
    {
        return BackendInterfaceNode::find()
            ->select(['id', 'sign', 'type'])
            ->andWhere(['=', 'is_deleted', 0])
            ->andWhere(['=', 'is_public', 0])
            ->asArray()
            ->all();
    }

    public static function allControllers($isDeleted = 0)
    {
        $query = BackendInterfaceNode::find()->andWhere([
            'type' => self::TYPE_CONTROLLER,
        ]);
        if ($isDeleted !== 'all') {
            $query->andWhere(['is_deleted' => $isDeleted]);
        }
        return $query->all();
    }

    public static function allActions($isDeleted = 0)
    {
        $query = BackendInterfaceNode::find()->andWhere([
            'type' => self::TYPE_ACTION,
        ]);
        if ($isDeleted !== 'all') {
            $query->andWhere(['is_deleted' => $isDeleted]);
        }
        return $query->all();
    }

    /**
     * 查询接口列表
     * @param $params
     */
    public static function search($params)
    {
        $offset = max(0, $params['page'] - 1) * $params['page_size'];
        $query = BackendInterfaceNode::find();
        if ($params['type'] != '-1') {
            $query->andWhere(['=', 't.type', intval($params['type'])]);
        }
        if (!empty($params['name'])) {
            $query->andWhere(['like', 't.name', "%{$params['name']}%"]);
        }
        if (!empty($params['sign'])) {
            $query->andWhere(['like', 't.sign', "%{$params['sign']}%"]);
        }
        if ($params['is_public'] != '-1') {
            $query->andWhere(['=', 't.is_public', intval($params['is_public'])]);
        }
        if ($params['is_deleted'] != '-1') {
            $query->andWhere(['=', 't.is_deleted', intval($params['is_deleted'])]);
        }
        $total = $query->count();
        $list = $query
            ->alias('t')
            ->select(['t.*', "ifnull(b.name,'系统') as operator_name"])
            ->leftJoin(\backend\models\User::tableName() . ' as b', 'b.id=t.operator_id')
            ->orderBy(['t.updated_time' => SORT_DESC])
            ->offset($offset)
            ->limit($params['page_size'])
            ->asArray()
            ->all();
        return [
            'total' => $total,
            'list' => $list,
        ];
    }

    private static function scandir($dir)
    {
        $list = [];
        foreach (scandir($dir) as $c) {
            if (in_array($c, ['.', '..'])) {
                continue;
            }

            if (is_dir($dir . '/' . $c)) {
                $list = ArrayHelper::merge($list, self::scandir($dir . '/' . $c));
            } else {
                $list[] = $dir . '/' . $c;
            }
        }
        return $list;
    }

    /**
     * 刷新接口列表
     */
    public static function refresh()
    {
        try {
            $user = User::getUser();
        } catch (UserException $e) {
            $user = null;
        }
        $operator_id = $user ? $user->id : 0;
        $nodes = self::generateNodes();

        //获取现有接口列表hash
        $controllerMap = ArrayHelper::listMap(self::allControllers('all'), 'sign');
        $actionMap = ArrayHelper::listMap(self::allActions('all'), 'sign');

        $addControllers = $addActions = $updates = $deletes = [];
        //添加或更新接口
        $nowControllerMap = $nowActionMap = [];
        foreach ($nodes as $node) {
            if ($node['type'] == self::TYPE_CONTROLLER) {
                $nowControllerMap[$node['sign']] = 1;
                if (!isset($controllerMap[$node['sign']])) {
                    //不存在则新增
                    $addControllers[] = $node;
                } else {
                    //存在则更新
                    $node['ar'] = $controllerMap[$node['sign']];
                    $updates[] = $node;
                }
            } else {
                $nowActionMap[$node['sign']] = 1;
                if (!isset($actionMap[$node['sign']])) {
                    //不存在则新增
                    $addActions[] = $node;
                } else {
                    //存在则更新
                    $node['ar'] = $actionMap[$node['sign']];
                    $updates[] = $node;
                }
            }
        }

        //删除已经不存在的接口
        foreach ($controllerMap as $key => $item) {
            if (!isset($nowControllerMap[$key])) {
                $deletes[] = $item['id'];
            }
        }
        foreach ($actionMap as $key => $item) {
            if (!isset($nowActionMap[$key])) {
                $deletes[] = $item['id'];
            }
        }

        //删除
        if ($deletes) {
            BackendInterfaceNode::updateAll([
                'is_deleted' => 1,
                'operator_id' => $operator_id,
            ], [
                'id' => $deletes,
            ]);
        }

        //新增控制器
        foreach ($addControllers as $add) {
            $m = new BackendInterfaceNode([
                'name' => $add['name'],
                'sign' => $add['sign'],
                'type' => $add['type'],
                'parent_id' => 0,
                'is_public' => $add['is_public'],
                'operator_id' => $operator_id,
            ]);
            $m->msave();
        }

        if ($addActions) {
            //新增方法
            $controllerMap = array_column(self::allControllers('all'), null, 'sign');
            foreach ($addActions as $add) {
                $m = new BackendInterfaceNode([
                    'name' => $add['name'],
                    'sign' => $add['sign'],
                    'type' => $add['type'],
                    'parent_id' => $controllerMap[$add['parent_sign']]['id'],
                    'is_public' => $add['is_public'],
                    'operator_id' => $operator_id,
                ]);
                $m->msave();
            }
        }

        $changed = [];
        if ($updates) {
            //更新
            $controllerMap = array_column(self::allControllers('all'), null, 'sign');
            foreach ($updates as $update) {
                /** @var BackendInterfaceNode $m */
                $m = $update['ar'];
                $m->name = $update['name'];
                $m->sign = $update['sign'];
                $m->type = $update['type'];
                $m->parent_id = isset($controllerMap[$update['parent_sign']]) ? $controllerMap[$update['parent_sign']]->id : 0;
                $m->is_public = $update['is_public'];
                $m->is_deleted = 0;
                if ($m->update() === 1) {
                    $m->operator_id = $operator_id;
                    $m->msave();
                    unset($update['ar']);
                    $changed[] = $update;
                }
            }
        }

        return true;
    }

    public static function generateNodes()
    {
        $controllers = self::scandir(self::getBaseDir());
        $nodes = [];
        foreach ($controllers as $controller) {
            $_nodes = self::parseNode(self::TYPE_CONTROLLER, $controller);
            $_nodes && $nodes = array_merge($nodes, $_nodes);
        }
        return $nodes;
    }

    private static function parseNode($type, $item, $parent = null)
    {
        $nodes = [];
        $methods = [];
        if ($type == self::TYPE_CONTROLLER) {
            $baseDir = self::getBaseDir();
            $controllerClass = str_replace([$baseDir . '/', '/', '.php'], ['', '\\', ''], $item);
            $reflector = new \ReflectionClass('backend\controllers\\' . $controllerClass);
            $doc = $reflector->getDocComment();
            //过滤控制器父类
            preg_match('/@baseController/', $doc, $match);
            if (!empty($match[0])) {
                return false;
            }
            $sign = StringHelper::toLineScore(str_replace(['Controller'], [''], $controllerClass));
            $methods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);
        } else {
            /** @var \ReflectionMethod $item */
            if ($item->getName() === 'actions') {
                return false;
            }

            //不是action过滤掉
            if (strpos($item->getName(), 'action') !== 0) {
                return false;
            }
            $doc = $item->getDocComment();
            $sign = StringHelper::toLineScore(str_replace(['action'], [''], $item->getName()));
        }

        // 非测试环境，过滤测试接口
        preg_match('/@test/', $doc, $match);
        $isTest = isset($match[0]) ? true : false;
        if ($isTest) {
            return false;
        }

        preg_match('/@public/', $doc, $match);
        $_isPublic = isset($match[0]) ? 1 : 0;
        preg_match('#\* ([^@|\n].+)#', $doc, $match);
        $name = $match[1] ?? $sign;

        if ($type == self::TYPE_CONTROLLER) {
            $_node = [
                'name' => $name,
                'sign' => $sign,
                'type' => $type,
                'is_public' => $_isPublic,
                'parent_sign' => '',
            ];
            $nodes[] = $_node;
            foreach ($methods as $method) {
                $_nodes = self::parseNode(self::TYPE_ACTION, $method, $_node);
                $_nodes && $nodes = array_merge($nodes, $_nodes);
            }
        } else {
            $nodes[] = [
                'name' => $name,
                'sign' => $parent['sign'] . '/' . $sign,
                'type' => $type,
                'is_public' => !empty($parent['is_public']) ? 1 : $_isPublic,
                'parent_sign' => $parent['sign'],
            ];
        }
        return $nodes;
    }

    /**
     * 获取公共接口
     */
    public static function getPublicNodes()
    {
        $nodeMap = BackendInterfaceNode::find()
            ->select(['sign', 'is_public'])
            ->where(['type' => self::TYPE_ACTION, 'is_public' => 1])
            ->asArray()
            ->indexBy('sign')
            ->all();

        return $nodeMap;
    }

    public static function getNodeTree()
    {
        $list = BackendInterfaceNode::find()
            ->select(['id', 'type', 'name', 'parent_id'])
            ->where(['is_deleted' => 0, 'is_public' => 0])
            ->asArray()
            ->all();

        $formedList = array_column($list, null, "id");
        return ArrayHelper::generateTree($formedList, "id", "parent_id");
    }
}