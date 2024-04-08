<?php
/**
 * Created by PhpStorm.
 * User: sidney
 * Date: 2019/11/5
 * Time: 4:38 PM
 */

namespace common\facade;


use common\exception\UrlEmptyException;
use common\helper\Env;
use yiqiang3344\yii2_lib\helper\ArrayHelper;
use yii\base\Model;

class Config extends Model
{
    /**
     * 获取配置文件配置
     * @param $name
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function getParams($name)
    {
        return \common\helper\config\Config::get($name);
    }

    /**
     * 获取开发人员通讯录
     * @param null $name
     * @return array
     * @throws \yii\base\Exception
     */
    public function getDeveloperAddressBook($name = null)
    {
        $ret = \common\helper\config\Config::getArray('developerAddressBook', []);
        return $name ? $ret[$name] : $ret;
    }

    /**
     * 根据业务类型获取监控人员通讯录
     * @return array
     * @throws \yii\base\Exception
     */
    public function getMonitorAddressBook()
    {
        $manages = [
            $this->getDeveloperAddressBook('易君强'),
            $this->getDeveloperAddressBook('魏建华'),
        ];
        $addressBook = Env::getBizMonitorAddressBook();
        if (empty($addressBook)) {
            return $manages;
        }
        $map = ArrayHelper::listMap($addressBook, 'name');
        foreach ($manages as $row) {
            if (isset($map[$row['name']])) {
                $addressBook[] = $row;
            }
        }
        return $addressBook;
    }

    /**
     * 获取API请求日志开关
     * @return bool
     */
    public function getApiAccessLogSwitch()
    {
        return true;
    }

    /**
     * 获取console执行日志开关
     * @return bool
     */
    public function getConsoleAccessLogSwitch()
    {
        return true;
    }

    /**
     * 获取验签开关
     * @return bool
     */
    public function getApiSignSwitch()
    {
        return \common\helper\config\Config::getString('switch.apiSign', 'off') == 'on';
    }

    /**
     * 获取api加密开关
     * @return bool
     */
    public function getApiEncryptSwitch()
    {
        return \common\helper\config\Config::getString('switch.apiEncrypt', 'off') == 'on';
    }

    /**
     * 获取响应加密开关
     * @return bool
     */
    public function getApiResponseEncrypt()
    {
        return \common\helper\config\Config::getString('switch.ApiResponseEncrypt', 'off') == 'on';
    }


    /**
     * 获取机构秘钥
     * @return bool
     */
    public function getSignKeyByUa($ua)
    {
        return '';
    }

    /**
     * 获取本地调试开关
     * @return string
     * @throws \yii\base\Exception
     */
    public function getLocalDebugSwitch()
    {
        return \common\helper\config\Config::getString('local_debug_switch', 'off');
    }

    /**
     * 获取内部系统域名，可替换环境，默认根据qa_env环境替换，qa_env 为空时，根据当前环境替换：prod->空，其他->test
     * @param $name
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function getInnerDomain($name)
    {
        $ret = \common\helper\config\Config::getString($name);
        if (empty($ret)) {
            throw new UrlEmptyException($name);
        }
        $replace = Env::getQaEnv() ?: (Env::getEnv() == 'prod' ? '' : 'test');
        return str_replace('env_place_holder', $replace, $ret);
    }
}