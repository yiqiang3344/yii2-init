<?php

namespace app\modules\user\tables;

/**
 * This is the model class for table "credit_user".
 *
 * @property string $id 用户id
 * @property string $name 昵称
 * @property int $user_id user表主键
 * @property string $mobile 手机号
 * @property string $email 邮箱
 * @property string $weixin_open_id 微信open id
 * @property string $gender 性别
 * @property string $person_id person ID
 * @property string $console_remark console 添加的用户备注
 * @property int $order_cnt 支付成单订单数
 * @property string $bill_status none 无账单 has_bill 有账单 has_overdue 有过逾期 overdue 正在逾期
 * @property int $is_get_authorize 1已获取权限
 * @property string $user_address_list 用户通讯录
 * @property string $bankNumber 银行卡号，目前国航需要
 * @property string $biz_event_status 用户业务事件状态
 * @property string $biz_event_time 用户业务事件触发时间
 * @property string $biz_event_data 用户业务事件数据
 * @property string $invitation_code 邀请码
 * @property string $used_invitation_code 用到的邀请码
 * @property string $source_type 来源类型：client 客户端，wap WAP端
 * @property string $app 应用标识
 * @property string $inner_app
 * @property string $os 系统：ios 苹果，android 安卓，other 其他
 * @property string $channel 应用市场渠道
 * @property string $utm_source 市场推广渠道
 * @property string $version_code 版本号
 * @property string $app_source app来源
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $last_login_time 用户最近登录时间
 * @property string $last_login_ip 用户最近登录ip
 * @property int $status 状态：10 正常，0 禁用
 * @property string $created_ip 用户创建ip
 * @property string $created_time 用户创建时间
 * @property string $updated_time 更新时间
 * @property int $created_at
 * @property int $updated_at
 * @property int $is_first_register 是否是首次注册1:是 0:否
 */
class CreditUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'credit_user';
    }
}