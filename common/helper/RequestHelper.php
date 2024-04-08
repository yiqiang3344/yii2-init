<?php
/**
 * Created by PhpStorm.
 * User: WeiDaoDao
 * Date: 2019/6/12
 * Time: 11:07 AM
 */

namespace common\helper;

use Yii;
use yii\base\UserException;

class RequestHelper extends BaseHelper
{
    public $appName; //app 名字, XYF
    public $appVersion; //app 版本, 3.4.1
    public $token; //token
    public $deviceID; // 设备ID
    public $OS;//iOS, android,h5
    public $channel;//app 来源
    public $idfa; //ios
    public $idfv; //ios
    public $deviceName; // 设备名字
    public $imei; //android
    public $OSVersion;//操作系统版本,iOS,android
    public $from;//app 来源
    public $sourceType;//客户端还是网页,client,wap
    public $versionCode;//app版本,10000
    public $XyfVersionCode; // 信用飞app版本号,10000

    public static $OSMap = ['ios', 'android', 'other'];
    public static $SOURCE_TYPE_MAPS = ['client', 'wap'];

    /**
     * @return bool
     * @throws UserException
     * @throws \yii\base\Exception
     */
    public function headersFilter()
    {
        $headers = Yii::$app->request->getHeaders();

        $this->appName = strtolower($headers->get('App-Name'));
//        if (strlen($this->appName) === 0) throw new UserException('header App-Name 不能为空');
        Env::setAttr('app', $this->appName);

        $this->appVersion = $headers->get('App-Version');
//        if (strlen($this->appVersion) === 0) throw new UserException('header App-Version 不能为空');
        Env::setAttr('app_version', $this->appVersion);

        $this->OS = strtolower($headers->get('OS', 'other'));
//        if (strlen($this->OS) === 0 || !in_array($this->OS, self::$OSMap)) throw new UserException('header OS 为空或格式不对');
        Env::setAttr('os', $this->OS);

        $this->sourceType = strtolower($headers->get('Source-Type'));
//        if (strlen($this->sourceType) === 0 || !in_array($this->sourceType, self::$SOURCE_TYPE_MAPS)) throw new UserException('header Source-Type 为空或格式不对');
        Env::setAttr('source_type', $this->sourceType);

        $this->channel = $headers->get('channel');
//        if (strlen($this->channel) === 0) throw new UserException('header channel 不能为空');
        Env::setAttr('channel', $this->channel);

        $this->from = $headers->get('from');
//        if (strlen($this->from) === 0) throw new UserException('header from 不能为空');
        Env::setAttr('from', $this->from);

        $this->versionCode = $headers->get('Version-Code');
//        if (strlen($this->versionCode) === 0) throw new UserException('header Version-Code 不能为空');
        Env::setAttr('version_code', $this->versionCode);

        $this->token = $headers->get('token');
        Env::setAttr('token', $this->token);
        $this->deviceID = $headers->get('Device-ID');
        Env::setAttr('device_id', $this->deviceID);
        $this->idfa = $headers->get('idfa');
        Env::setAttr('idfa', $this->idfa);
        $this->idfv = $headers->get('idfv');
        Env::setAttr('idfv', $this->idfv);
        $this->imei = $headers->get('imei');
        Env::setAttr('imei', $this->imei);
        $this->deviceName = $headers->get('Device-Name');
        $this->OSVersion = $headers->get('OS-Version');
        $this->XyfVersionCode = $headers->get('Xyf-Version-Code');

        if ($this->OS != 'other') {
            if (strlen($this->idfa) === 0 && strlen($this->imei) === 0) throw new UserException('header idfa 或 imei 不能为空');
        }

        return true;
    }
}