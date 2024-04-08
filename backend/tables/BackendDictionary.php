<?php

namespace backend\tables;

use Yii;

/**
 * This is the model class for table "{{%backend_dictionary}}".
 *
 * @property int $id
 * @property string $sign 标识
 * @property string $name 名称
 * @property string $comment 说明
 * @property int $parent_id 父ID,0表示没有父节点
 * @property int $is_deleted 是否删除：1 是，0 否
 * @property int $operator_id 操作人ID
 * @property string $created_time 创建时间
 * @property string $updated_time 更新时间
 */
class BackendDictionary extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%backend_dictionary}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('backend');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sign', 'name', 'comment', 'parent_id'], 'required'],
            [['parent_id', 'is_deleted', 'operator_id'], 'integer'],
            [['created_time', 'updated_time'], 'safe'],
            [['sign', 'name'], 'string', 'max' => 64],
            [['comment'], 'string', 'max' => 255],
            [['sign'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sign' => 'Sign',
            'name' => 'Name',
            'comment' => 'Comment',
            'parent_id' => 'Parent ID',
            'is_deleted' => 'Is Deleted',
            'operator_id' => 'Operator ID',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
