<?php

namespace backend\facade;


use common\helper\webclient\WebClient;

class DingTalk
{
    const APP_ID = 'dingbxdbia4yxyqtmcbs';
    const APP_SECRET = '3bkCENSHqJxcYpaqr9kSVrH4QA0VW6oltpLESXPN5iMw1vAIUVZL7dkmhOSQRYGi';

    const GET_ACCESS_TOKEN = 'https://oapi.dingtalk.com/gettoken';
    const GET_USER_DETAIL = 'https://oapi.dingtalk.com/user/get';
    const GET_USER_ID = 'https://oapi.dingtalk.com/user/getUseridByUnionid';

    private static $token;

    /**
     * @return mixed
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    protected static function getAppAccessToken()
    {
        if (!self::$token) {
            $url = sprintf("%s?appkey=%s&appsecret=%s", self::GET_ACCESS_TOKEN, self::APP_ID, self::APP_SECRET);
            $data = WebClient::get($url);
            if (!isset($data["errcode"]) || $data["errcode"] != 0) {
                throw new \Exception("获取access_token失败");
            }
            self::$token = $data["access_token"];
        }
        return self::$token;
    }

    /**
     * @param $userid
     * @return array|bool
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public static function getUserDetail($userid)
    {
        $accessToken = self::getAppAccessToken();
        $url = sprintf("%s?access_token=%s&userid=%s",
            self::GET_USER_DETAIL, $accessToken, $userid);
        $data = WebClient::get($url);
        if (!isset($data["errcode"]) || $data["errcode"] != 0) {
            throw new \Exception("获取用户详情失败");
        }
        return $data;
    }

    /**
     * @param $unionId
     * @return mixed
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public static function getUserId($unionId)
    {
        $accessToken = self::getAppAccessToken();
        $url = sprintf("%s?access_token=%s&unionid=%s",
            self::GET_USER_ID, $accessToken, $unionId);
        $data = WebClient::get($url);
        if (!isset($data["errcode"]) || $data["errcode"] != 0) {
            throw new \Exception("获取用户id失败");
        }
        return $data['userid'];
    }
}
