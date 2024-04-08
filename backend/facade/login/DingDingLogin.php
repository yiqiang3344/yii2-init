<?php

namespace backend\facade\login;

use backend\facade\DingTalk;
use common\helper\webclient\WebClient;
use common\logging\DebugLog;
use yii\base\Model;
use yii\base\UserException;

class DingDingLogin extends Model
{
    const APP_ID = 'dingoadylboz6czt9us9nt';
    const APP_SECRET = 'qVhARUOFgsCERu0rmBtBA8eWYeX8EbptM2TBqSS7PdbLtCTBK5S1JJmuuo9O63Ty';

    const URL_USER_INFO = 'https://oapi.dingtalk.com/sns/getuserinfo_bycode';

    public function getUserInfo($code)
    {
        $data = [
            'tmp_auth_code' => $code,
        ];

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $now = time() * 1000;
        $url = sprintf("%s?accessKey=%s&timestamp=%s&signature=%s",
            self::URL_USER_INFO,
            self::APP_ID,
            $now,
            self::sign($now));

        //获取用户信息
        $userLoginInfo = WebClient::post($url, $data, $headers);
        if (!isset($userLoginInfo['errcode']) || $userLoginInfo['errcode'] != 0) {
            DebugLog::instance()->log('获取用户信息失败[' . ($userLoginInfo['errcode'] ?? 'null') . ']', __CLASS__);
            throw new UserException('获取钉钉用户信息失败');
        }
        $userInfo = $userLoginInfo["user_info"];
        $userId = DingTalk::getUserId($userInfo["unionid"]);
        $userDetail = DingTalk::getUserDetail($userId);

        $userInfo["open_id"] = $userInfo["openid"];
        $userInfo["name"] = $userInfo["nick"];
        $userInfo["mobile"] = $userDetail["mobile"];
        $userInfo["avatar_url"] = $userDetail["avatar"];
        return $userInfo;
    }

    // 计算签名
    public static function sign($time)
    {
        $s = hash_hmac('sha256', $time, self::APP_SECRET, true);
        $signature = base64_encode($s);
        return urlencode($signature);
    }
}