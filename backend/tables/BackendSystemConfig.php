<?php

namespace backend\tables;

use Yii;

/**
 * This is the model class for table "{{%backend_system_config}}".
 *
 * @property int $id
 * @property string $name 名称
 * @property string $value 值
 * @property int $operator_id 操作人ID
 * @property string $created_time 创建时间
 * @property string $updated_time 更新时间
 */
class BackendSystemConfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%backend_system_config}}';
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
            [['operator_id'], 'integer'],
            [['created_time', 'updated_time'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['value'], 'string', 'max' => 255],
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
            'value' => 'Value',
            'operator_id' => 'Operator ID',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
