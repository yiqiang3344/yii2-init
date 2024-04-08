<?php

namespace backend\tables;

use Yii;

/**
 * This is the model class for table "{{%backend_menu_node}}".
 *
 * @property int $id
 * @property string $name 名称
 * @property int $type 类型：1 目录，2 页面，3 按钮
 * @property int $parent_id 父ID,0表示没有父节点
 * @property string $route 路由
 * @property string $icon 图标
 * @property int $is_public 是否公开
 * @property int $order 排序
 * @property int $is_hide 是否隐藏
 * @property int $is_deleted 是否删除
 * @property int $operator_id 操作人ID
 * @property string $created_time 创建时间
 * @property string $updated_time 更新时间
 */
class BackendMenuNode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%backend_menu_node}}';
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
            [['name', 'type'], 'required'],
            [['type', 'parent_id', 'is_public', 'order', 'is_hide', 'is_deleted', 'operator_id'], 'integer'],
            [['created_time', 'updated_time'], 'safe'],
            [['name', 'route', 'icon'], 'string', 'max' => 64],
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
            'type' => 'Type',
            'parent_id' => 'Parent ID',
            'route' => 'Route',
            'icon' => 'Icon',
            'is_public' => 'Is Public',
            'order' => 'Order',
            'is_hide' => 'Is Hide',
            'is_deleted' => 'Is Deleted',
            'operator_id' => 'Operator ID',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
