<?php

namespace backend\tables;

use Yii;

/**
 * This is the model class for table "{{%backend_role_node}}".
 *
 * @property int $id
 * @property int $role_id 角色ID
 * @property int $type 类型：1 菜单，2 接口
 * @property int $node_id 权限ID
 * @property int $is_deleted 是否删除：1 是，0 否
 * @property int $operator_id 操作人ID
 * @property string $created_time 创建时间
 * @property string $updated_time 更新时间
 */
class BackendRoleNode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%backend_role_node}}';
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
            [['role_id', 'type', 'node_id'], 'required'],
            [['role_id', 'type', 'node_id', 'is_deleted', 'operator_id'], 'integer'],
            [['created_time', 'updated_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_id' => 'Role ID',
            'type' => 'Type',
            'node_id' => 'Node ID',
            'is_deleted' => 'Is Deleted',
            'operator_id' => 'Operator ID',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
