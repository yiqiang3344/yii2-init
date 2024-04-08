<?php
/**
 * Created by PhpStorm.
 * User: sidney
 * Date: 2020/1/10
 * Time: 4:48 PM
 */

namespace api\modules\user\facade;


use api\modules\user\models\UserFactory;
use common\helper\AppHelper;
use common\helper\RequestHelper;
use Yii;
use yii\base\Model;

class User extends Model
{
    /**
     * @param $mobile
     * @param $appName
     * @return array|null|\yii\db\ActiveRecord
     * @throws \Exception
     */
    public function loginOrRegister($mobile, $appName)
    {
        $userTable = AppHelper::getUserTableByAppName($appName);
        $userModel = UserFactory::create($userTable);
        $userInfo = $userModel->find()->where(['mobile' => $mobile, 'app' => $appName])->one();

        if (empty($userInfo)) {
            //新用户,直接创建
            $userInfo = self::create($mobile, $appName);
        } else {
            //老用户
            $userInfo = $userInfo->toArray();
        }

        return $userInfo;
    }

    /**
     * @param $mobile
     * @param $appName
     * @return array
     * @throws \Exception
     */
    public function create($mobile, $appName)
    {
        switch ($appName)
        {
            case 'hb' :
                return self::createUserByHB($mobile);
                break;
            default :
                throw new \Exception("{$appName} : 创建用户失败.");
        }
    }

    /**
     * @param $mobile
     * @return array
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function createUserByHB($mobile)
    {
        $requestHelper = RequestHelper::instance();
        $request = Yii::$app->request;
        $userIP = $request->getUserIP();

        //开启事务
        $transaction = Yii::$app->db->beginTransaction();

        $userModel = UserFactory::create('User');
        $userInfo = $userModel->find()->where(['mobile' => $mobile])->one();
        if (empty($userInfo)) {
            //为空创建创建 User
            $userModel->mobile = $mobile;
            $userModel->created_time = date('Y-m-D H:i:s');
            $userModel->created_ip = $userIP;
            $userModel->last_login_time = date('Y-m-D H:i:s');;
            $userModel->last_login_ip = $userIP;
            $userModel->version_code = $requestHelper->versionCode;
            $userModel->utm_source = $requestHelper->channel;
            $userSaveRet = $userModel->save();
            if (!$userSaveRet) throw new \Exception('创建用户失败', 200001);
            $userID = $userModel->id;
        } else {
            //已经存在
            $userID = $userInfo->id;
        }

        //创建 create_user
        $creditUserModel = UserFactory::create('credit_user');
        $creditUserModel->user_id = $userID;
        $creditUserModel->mobile = $mobile;
        $creditUserModel->app = $requestHelper->appName;
        $creditUserModel->inner_app = $requestHelper->appName;
        $creditUserModel->os = $requestHelper->OS;
        $creditUserModel->channel = $requestHelper->channel;
        $creditUserModel->utm_source = $requestHelper->channel;
        $creditUserModel->version_code = $requestHelper->versionCode;
        $creditUserModel->created_time = date('Y-m-d H:i:s');
        $creditUserModel->created_ip = $userIP;
        $creditUserModel->last_login_time = date('Y-m-d H:i:s');
        $creditUserModel->last_login_ip = $userIP;
        $creditUserModel->updated_time = date('Y-m-d H:i:s');
        $creditUserModel->created_at = time();
        $creditUserModel->updated_at = time();

        $creditUserSaveRet = $creditUserModel->save();
        if (!$creditUserSaveRet) throw new \Exception('创建用户失败', 200001);
        $transaction->commit();

        return $creditUserModel->toArray();
    }
}