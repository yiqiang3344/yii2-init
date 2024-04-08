<?php

namespace backend\tables;

use Yii;

/**
 * This is the model class for table "{{%backend_user}}".
 *
 * @property int $id
 * @property string $name 姓名
 * @property string $mobile 手机
 * @property string $password 密码
 * @property int $password_expire 密码过期时间，0不过期
 * @property int $is_deleted 是否禁用：1 是，0 否
 * @property int $operator_id 操作人ID
 * @property string $created_time 创建时间
 * @property string $updated_time 更新时间
 */
class BackendUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%backend_user}}';
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
            [['name', 'mobile', 'password', 'password_expire'], 'required'],
            [['password_expire', 'is_deleted', 'operator_id'], 'integer'],
            [['created_time', 'updated_time'], 'safe'],
            [['name', 'password'], 'string', 'max' => 64],
            [['mobile'], 'string', 'max' => 32],
            [['mobile'], 'unique'],
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
            'mobile' => 'Mobile',
            'password' => 'Password',
            'password_expire' => 'Password Expire',
            'is_deleted' => 'Is Deleted',
            'operator_id' => 'Operator ID',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
