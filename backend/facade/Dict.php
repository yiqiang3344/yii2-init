<?php

namespace backend\facade;


use backend\models\BackendDictionary;
use common\helper\ArrayHelper;
use yii\base\Model;
use yii\base\UserException;

class Dict extends Model
{
    public static function getListByParentId($pid)
    {
        return BackendDictionary::find()
            ->select(['id', 'sign', 'name', 'comment', 'parent_id'])
            ->andWhere(["parent_id" => $pid])
            ->andWhere(["is_deleted" => 0])
            ->orderBy(['id' => SORT_ASC])
            ->asArray()
            ->all();
    }

    /**
     * @param $sign
     * @return BackendDictionary|null
     */
    public static function getBySign($sign)
    {
        return BackendDictionary::findOne([
            'sign' => $sign,
            'is_deleted' => 0,
        ]);
    }

    /**
     * 获取所有子集
     * @param $id
     * @param $list
     */
    public static function getListById($id, &$list)
    {
        $data = self::getListByParentId($id);
        $list = array_merge($list, $data);
        foreach ($data as $item) {
            self::getListById($item["id"], $list);
        }
    }

    public static function getDataBySign($sign)
    {
        $item = self::getBySign($sign);
        if (!$item) {
            throw new UserException('字典不存在');
        }
        $list = [];
        self::getListById($item->id, $list);
        $formedList = array_column($list, null, "id");
        return ArrayHelper::generateTree($formedList, "id", "parent_id");
    }

    public static function search($params)
    {
        $offset = max(0, $params['page'] - 1) * $params['page_size'];
        $query = BackendDictionary::find();
        if (!empty($params['sign'])) {
            $query->andWhere(['=', 'sign', $params['sign']]);
        }
        if (!empty($params['name'])) {
            $query->andWhere(['=', 'name', $params['name']]);
        }
        if (!empty($params['parent_id'])) {
            $query->andWhere(['=', 'parent_id', $params['parent_id']]);
        }
        $total = $query->count();
        $list = $query
            ->alias('t')
            ->select(['t.*', "ifnull(b.name,'系统') as operator_name"])
            ->leftJoin(\backend\models\User::tableName() . ' as b', 'b.id=t.operator_id')
            ->orderBy(['t.id' => SORT_DESC])
            ->offset($offset)
            ->limit($params['page_size'])
            ->asArray()
            ->all();
        return [
            'total' => $total,
            'list' => $list,
        ];
    }

    public static function add($params)
    {
        //检查父ID是否存在
        if ($params['parent_id'] && !BackendDictionary::find()->where(['id' => $params['parent_id'], 'is_deleted' => 0])->exists()) {
            throw new UserException('父ID不存在，或已禁用');
        }

        $params['parent_id'] = intval($params['parent_id']);
        $m = new BackendDictionary(ArrayHelper::cp($params, [
            'sign',
            'name',
            'comment',
            'parent_id',
        ]));
        $m->operator_id = User::getCurrentUserId();
        $m->is_deleted = $params['status'] ? 0 : 1;
        $m->msave();
        return $m;
    }

    public static function edit($params)
    {
        //检查父ID是否存在
        if ($params['parent_id'] && !BackendDictionary::find()->where(['id' => $params['parent_id'], 'is_deleted' => 0])->exists()) {
            throw new UserException('父ID不存在，或已禁用');
        }

        $m = BackendDictionary::findOne($params['id']);
        if (!$m) {
            throw new UserException('字典不存在');
        }

        $params['parent_id'] = intval($params['parent_id']);
        $m->setAttributes(ArrayHelper::cp($params, [
            'sign',
            'name',
            'comment',
            'parent_id',
        ]));
        $m->operator_id = User::getCurrentUserId();
        $m->msave();
        return $m;
    }

    public static function changeStatus($params)
    {
        $m = BackendDictionary::findOne($params['id']);
        if (!$m) {
            throw new UserException('字典不存在');
        }

        $m->is_deleted = $params['status'] ? 0 : 1;
        $m->operator_id = User::getCurrentUserId();
        $m->msave();
        return $m;
    }
}