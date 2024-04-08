<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/11
 * Time: 11:22 AM
 */

namespace common\facade;

use common\exception\CUserException;
use common\helper\Env;
use common\helper\db\DB;
use common\helper\JwtHelper;
use common\logging\DebugLog;
use common\models\User;
use yii\base\Model;

/**
 * 用户中台
 * Class UserMiddle
 * @package common\unit
 */
class UserMiddle extends Model
{
    private $_current_user;

    /**
     * @param $id
     * @return User
     * @throws \yii\db\Exception
     */
    public function getUserById($id)
    {
        //TODO 中台暂时不支持，只能自己先从数据库访问
        $attributes = DB::default()->createCommand("select u.id,u.mobile,u.app,u.name as nickname,p.id_card_number,p.name,u.created_ip from credit_user u left join Person p on p.id=u.person_id where u.id=:id", [
            ':id' => $id,
        ])->queryOne();

        return new User($attributes);
    }

    /**
     * 获取当前用户
     * @return User|null
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function getCurrentUser()
    {
        if (!$this->_current_user) {
            if (!Env::getToken()) {
                return null;
            }
            $data = (array)JwtHelper::validateToken(Env::getToken());
            DebugLog::instance()->info('用户登录信息：' . json_encode($data, JSON_UNESCAPED_UNICODE), __FUNCTION__);

            //TODO 中台暂时不支持，只能自己先从数据库访问
            $attributes = DB::default()->createCommand("select u.id,u.mobile,u.app,u.inner_app,u.name as nickname,p.id_card_number,p.name,u.created_ip from credit_user u left join Person p on p.id=u.person_id where u.mobile=:mobile and u.app=:app", [
                ':mobile' => $data['mobile'],
                ':app' => $data['app'],
            ])->queryOne();

            if (!$attributes) {
                throw new CUserException('用户登录失败');
            }
            $this->_current_user = new User($attributes);
        }
        return $this->_current_user;
    }
}