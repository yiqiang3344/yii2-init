<?php

namespace backend\helper;


class Tuomin
{
    const NAME = 1; //姓名
    const MOBILE = 2; //手机号
    const ID_CARD_NO = 3; //身份证号
    const BANK_CARD_NO = 4; //银行卡
    const ADDRESS = 5; //住址
    const OFFICIAL_CARD_NO = 6; //军官证

    /**
     * 用户数据脱敏
     * @param $dataType 1 姓名、2 手机号、3 身份证号、4 银行卡、5 住址、6 军官证
     * @param string $data 原始数据
     * @return string
     */
    public static function encrypt($dataType, $data)
    {
        if (empty($data)) return '';
        $mbStrLen = mb_strlen($data);
        if ($dataType == self::NAME) {
            if ($mbStrLen <= 3) {
                $otherLength = $mbStrLen - 1;
                $data = "*" . mb_substr($data, -$otherLength);
            } else if ($mbStrLen <= 6 && $mbStrLen > 3) {
                $otherLength = $mbStrLen - 2;
                $data = str_pad('*', $otherLength, '*') . mb_substr($data, -2);
            } else {
                $otherLength = $mbStrLen - 3;
                $data = mb_substr($data, 0, 1) . str_pad('*', $otherLength, '*') . mb_substr($data, -2);
            }
        } else if ($dataType == self::MOBILE) {
            $data = substr($data, 0, 3) . '****' . substr($data, -4);
        } else if ($dataType == self::ID_CARD_NO) {
            $data = substr($data, 0, 6) . '*********' . substr($data, -2);
        } else if ($dataType == self::BANK_CARD_NO) {
            $data = substr($data, 0, 6) . '******' . substr($data, -4);
        } else if ($dataType == self::ADDRESS) {
            if ($mbStrLen >= 12) {
                $otherLength = $mbStrLen - 6;
                $data = mb_substr($data, 0, 6) . str_pad('*', $otherLength, '*');
            } else {
                $data = mb_substr($data, 0, ceil($mbStrLen / 2)) . '*******';
            }
        } else if ($dataType == self::OFFICIAL_CARD_NO) {
            $data = '****' . substr($data, -2);
        } else {
            $data = '';
        }
        return $data;
    }
}