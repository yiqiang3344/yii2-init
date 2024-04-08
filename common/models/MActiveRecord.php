<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/3/27
 * Time: 4:00 PM
 */

namespace common\models;


use common\exception\DBException;

trait MActiveRecord
{
    /**
     * @param $id
     * @param bool $col
     * @return mixed
     */
    public static function findByIdForUpdate($id, $col = false)
    {
        return static::findBySql("select * from " . static::tableName() . " where " . ($col ?: 'id') . "=:id for update", [':id' => $id])->one();
    }

    /**
     * 自定义保存方法，保存失败抛出异常
     * @return bool
     * @throws DBException
     */
    public function msave()
    {
        if (!$this->save()) {
            throw new DBException(json_encode($this->getErrors(), JSON_UNESCAPED_UNICODE));
        }
        return true;
    }
}