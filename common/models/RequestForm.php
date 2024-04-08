<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/6/8
 * Time: 3:16 PM
 */

namespace common\models;

use yii\base\Model;

/**
 * 请求头过滤
 * Class RequestForm
 * @package common\models
 */
class RequestForm extends Model
{
    public $appName = ''; //app 名字, XYF
    public $appVersion = ''; //app 版本, 3.4.1
    public $token; //token
    public $deviceID; // 设备ID
    public $OS;//iOS, android,h5
    public $userAgent;
    public $versionCode;
    public $UID;
    public $language;//语言
    public $sourceType;//app 来源
    public $idfa; //ios
    public $idfv; //ios
    public $deviceName; // 设备名字
    public $imei; //android
    public $OSVersion = '';//操作系统版本,iOS,android
    public $host ;

    public function rules()
    {
        return [
            [['app-name', 'app-version', 'os', 'host'], 'required'],
        ];
    }

    public static function headersDataFormatting($headers)
    {
        $data = [];
        foreach ($headers as $key => $header) {
            $data[$key] = $header[0];
        }
        return $data;
    }

    public static function headersFilter()
    {

    }
}