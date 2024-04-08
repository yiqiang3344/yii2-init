<?php

namespace backend\facade;

use backend\facade\login\DingDingLogin;
use backend\models\BackendSystemConfig;
use backend\models\User;
use common\facade\Captcha;
use common\helper\redis\Redis;
use yiqiang3344\yii2_lib\helper\Time;
use yii\base\Exception;
use yii\base\UserException;

class Login
{
    const LOGIN_METHOD_PASSWORD = 1;
    const LOGIN_METHOD_CAPTCHA = 2;
    const LOGIN_METHOD_DINGDING = 3;
    const LOGIN_METHODS = [
        self::LOGIN_METHOD_PASSWORD => '密码',
        self::LOGIN_METHOD_CAPTCHA => '短信验证码',
        self::LOGIN_METHOD_DINGDING => '钉钉扫码',
    ];

    const LOGIN_EXPIRE_HASH = [
        '小时' => 3600,
        '天' => 86400,
        '月' => 86400 * 30,
    ];
    const PWD_EXPIRE_HASH = [
        '1个月' => 86400 * 30,
        '3个月' => 86400 * 30 * 3,
        '6个月' => 86400 * 30 * 6,
        '1年' => 86400 * 30 * 12,
        '永久' => 0,
    ];

    /**
     * 登录方式列表
     */
    public static function getLoginMethods()
    {
        $name = 'LoginMethods';
        $m = BackendSystemConfig::findOne(['name' => $name]);
        if (!$m) {
            return [
                self::LOGIN_METHOD_PASSWORD,
            ];
        }
        $ret = [];
        $list = json_decode($m->value, true);
        foreach ($list as $item) {
            if (!isset(self::LOGIN_METHODS[$item])) {
                continue;
            }
            $ret[] = $item;
        }
        return $ret;
    }

    /**
     * 登录方式列表
     */
    public static function setLoginMethods($list)
    {
        $name = 'LoginMethods';
        $m = BackendSystemConfig::findOne(['name' => $name]);
        if (!$m) {
            $m = new BackendSystemConfig([
                'name' => $name,
            ]);
        }
        foreach ($list as $item) {
            if (!isset(self::LOGIN_METHODS[$item])) {
                throw new UserException('不合法的值:' . $item);
            }
        }
        $m->value = json_encode($list, JSON_UNESCAPED_UNICODE);
        $m->operator_id = \backend\facade\User::getUser()->operator_id;
        $m->msave();
        return true;
    }

    /**
     * 密码登录
     */
    public static function loginByPassword($mobile, $password)
    {
        //判断是否可用
        if (!in_array(Login::LOGIN_METHOD_PASSWORD, Login::getLoginMethods())) {
            throw new UserException('不支持此登录方式');
        }

        $user = User::findIdentityByAccessToken($mobile, User::TYPE_MOBILE);
        if (!$user) {
            throw new UserException('用户不存在');
        }

        if (!$user->validatePassword($password)) {
            throw new UserException('密码错误');
        }

        return [
            'token' => $user->generateToken(),
            'info' => $user->getInfo(),
            'menu' => $user->getMenus(),
        ];
    }

    /**
     * 发送短信验证码
     */
    public static function sendCaptcha($mobile)
    {
        //判断是否可用
        if (!in_array(Login::LOGIN_METHOD_CAPTCHA, Login::getLoginMethods())) {
            throw new UserException('不支持此登录方式');
        }

        $user = User::findIdentityByAccessToken($mobile, User::TYPE_MOBILE);
        if (!$user) {
            throw new UserException('用户不存在');
        }

        Captcha::sendSmsCode($mobile, 'login');
        return true;
    }

    /**
     * 验证码登录
     */
    public static function loginByCaptcha($mobile, $captcha)
    {
        //判断是否可用
        if (!in_array(Login::LOGIN_METHOD_CAPTCHA, Login::getLoginMethods())) {
            throw new UserException('不支持此登录方式');
        }

        //检查验证码
        Captcha::validate($mobile, 'login', $captcha);

        $user = User::findIdentityByAccessToken($mobile, User::TYPE_MOBILE);
        if (!$user) {
            throw new UserException('用户不存在');
        }

        return [
            'token' => $user->generateToken(),
            'info' => $user->getInfo(),
            'menu' => $user->getMenus(),
        ];
    }

    /**
     * 钉钉扫码登录
     */
    public static function loginByDingding($code)
    {
        //判断是否可用
        if (!in_array(Login::LOGIN_METHOD_DINGDING, Login::getLoginMethods())) {
            throw new UserException('不支持此登录方式');
        }

        $userInfo = DingDingLogin::instance()->getUserInfo($code);
        $user = User::findIdentityByAccessToken($userInfo['mobile'], User::TYPE_MOBILE);
        if (!$user) {
            throw new UserException('用户不存在');
        }
        return [
            'token' => $user->generateToken(),
            'info' => $user->getInfo(),
            'menu' => $user->getMenus(),
        ];
    }

    public static function getLoginExpireInfo()
    {
        $name = 'LoginExpireInfo';
        $m = BackendSystemConfig::findOne(['name' => $name]);
        if (!$m) {
            return [
                'no_action_num' => 2,
                'no_action_unit' => '小时',
                'has_action_num' => 8,
                'has_action_unit' => '小时',
            ];
        }
        return json_decode($m->value, true);
    }

    public static function getLoginExpireByType($type)
    {
        $data = self::getLoginExpireInfo();
        if ($type == 'no_action') {
            $ret = $data['no_action_num'] * self::LOGIN_EXPIRE_HASH[$data['no_action_unit']];
        } elseif ($type == 'has_action') {
            $ret = $data['has_action_num'] * self::LOGIN_EXPIRE_HASH[$data['has_action_unit']];
        } else {
            throw new Exception('类型不存在:' . $type);
        }
        return $ret;
    }

    public static function setLoginExpireInfo($data)
    {
        //检查参数
        if (!isset(self::LOGIN_EXPIRE_HASH[$data['no_action_unit']])) {
            throw new UserException('no_action_unit值不合法');
        }
        if ($data['no_action_num'] <= 0) {
            throw new UserException('no_action_num值不合法');
        }
        if (!isset(self::LOGIN_EXPIRE_HASH[$data['has_action_unit']])) {
            throw new UserException('has_action_unit值不合法');
        }
        if ($data['has_action_num'] <= 0) {
            throw new UserException('has_action_num值不合法');
        }

        $name = 'LoginExpireInfo';
        $m = BackendSystemConfig::findOne(['name' => $name]);
        if (!$m) {
            $m = new BackendSystemConfig([
                'name' => $name,
            ]);
        }
        $m->value = json_encode([
            'no_action_num' => ceil($data['no_action_num']),
            'no_action_unit' => $data['no_action_unit'],
            'has_action_num' => ceil($data['has_action_num']),
            'has_action_unit' => $data['has_action_unit'],
        ], JSON_UNESCAPED_UNICODE);
        $m->operator_id = \backend\facade\User::getUser()->operator_id;
        $m->msave();
        return true;
    }

    public static function getPwdExpire()
    {
        $name = 'PwdExpireInfo';
        $m = BackendSystemConfig::findOne(['name' => $name]);
        if (!$m) {
            return '永久';
        }
        return $m->value;
    }

    public static function getPwdExpireTime()
    {
        $expireInterval = self::PWD_EXPIRE_HASH[self::getPwdExpire()];
        return $expireInterval > 0 ? (Time::time() + $expireInterval) : 0;
    }

    public static function setPwdExpire($expire)
    {
        //检查参数
        if (!in_array($expire, ['1个月', '3个月', '6个月', '1年', '永久'])) {
            throw new UserException('值不合法');
        }

        $name = 'PwdExpireInfo';
        $m = BackendSystemConfig::findOne(['name' => $name]);
        if (!$m) {
            $m = new BackendSystemConfig([
                'name' => $name,
            ]);
        }
        $m->value = $expire;
        $m->operator_id = \backend\facade\User::getUser()->operator_id;
        $m->msave();
        return true;
    }

    public static function getInitPwd()
    {
        $name = 'InitPwd';
        $m = BackendSystemConfig::findOne(['name' => $name]);
        if (!$m) {
            return '12345678';
        }
        return $m->value;
    }

    public static function setInitPwd($password)
    {
        $name = 'InitPwd';
        $m = BackendSystemConfig::findOne(['name' => $name]);
        if (!$m) {
            $m = new BackendSystemConfig([
                'name' => $name,
            ]);
        }
        $m->value = $password;
        $m->operator_id = \backend\facade\User::getUser()->operator_id;
        $m->msave();
        return true;
    }
}