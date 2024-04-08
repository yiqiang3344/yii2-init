<?php

namespace backend\tables;

use Yii;

/**
 * This is the model class for table "{{%backend_operation_record}}".
 *
 * @property int $id
 * @property string $menu 菜单
 * @property string $interface_name 接口名称
 * @property string $interface_sign 接口标识
 * @property string $request 请求参数
 * @property string $response 响应信息
 * @property string $ip ip
 * @property int $operator_id 操作人ID
 * @property string $operator_name 操作人姓名
 * @property string $created_time 创建时间
 * @property string $updated_time 更新时间
 */
class BackendOperationRecord extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%backend_operation_record}}';
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
            [['menu', 'interface_name', 'interface_sign', 'ip'], 'required'],
            [['request', 'response'], 'string'],
            [['operator_id'], 'integer'],
            [['created_time', 'updated_time'], 'safe'],
            [['menu'], 'string', 'max' => 64],
            [['interface_name', 'interface_sign'], 'string', 'max' => 128],
            [['ip', 'operator_name'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'menu' => 'Menu',
            'interface_name' => 'Interface Name',
            'interface_sign' => 'Interface Sign',
            'request' => 'Request',
            'response' => 'Response',
            'ip' => 'Ip',
            'operator_id' => 'Operator ID',
            'operator_name' => 'Operator Name',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
