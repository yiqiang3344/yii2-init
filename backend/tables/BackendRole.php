<?php

namespace backend\tables;

use Yii;

/**
 * This is the model class for table "{{%backend_role}}".
 *
 * @property int $id
 * @property string $name 名称
 * @property string $comment 描述
 * @property int $is_deleted 是否删除：1 是，0 否
 * @property int $operator_id 操作人ID
 * @property string $created_time 创建时间
 * @property string $updated_time 更新时间
 */
class BackendRole extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%backend_role}}';
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
            [['name', 'comment'], 'required'],
            [['is_deleted', 'operator_id'], 'integer'],
            [['created_time', 'updated_time'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['comment'], 'string', 'max' => 128],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'comment' => 'Comment',
            'is_deleted' => 'Is Deleted',
            'operator_id' => 'Operator ID',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
