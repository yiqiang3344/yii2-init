<?php

namespace common\helper\webclient;



class PayClient
{
    /**
     * 调用支付系统
     * @param $payUrl
     * @param array $arr
     * @return array
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public static function call($payUrl, array $arr)
    {
        $arr['sign'] = static::sign($arr);
        return WebClient::post($payUrl, $arr);
    }

    /**
     * 发起收单请求
     * @param array $arr
     * @param $payUrl
     * @return array
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public static function create(array $arr, $payUrl)
    {
        $arr['sign'] = static::sign($arr);
        if (in_array($arr['pay_channel_list'], ['wx', 'icbc'])) {
            return WebClient::post($payUrl, $arr);
        } else {
            self::postForm($payUrl, $arr);
        }
    }

    // 验签
    public static function verify($arr)
    {
        if (!isset($arr['sign'])) {
            return false;
        }
        $sign = $arr['sign'];
        unset($arr['sign']);
        return $sign == static::sign($arr);
    }

    // 签名函数
    public static function sign(array $postArray) {
        $privateKey = '012345678901234567890123'; // todo：目前使用首付游相同的方法
        ksort($postArray);
        $sign = json_encode($postArray, JSON_UNESCAPED_UNICODE);
        return hash('sha256', $sign . $privateKey);
    }

    public static function postForm($payUrl, array $arr)
    {
        $input = '';
        foreach ($arr as $k=>$v) {
            $input .= "<input type='hidden' name='$k' value='$v'>";
        }

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>支付系统 - 收单网关</title>
</head>
<body onload="autosubmit()">
    <form method="post" action="$payUrl" id="payForm">
        $input
    </form>
    <script>
        function autosubmit() {
            document.getElementById('payForm').submit();
        }
    </script>
</body>
</html>
HTML;

        echo $html;
    }
}