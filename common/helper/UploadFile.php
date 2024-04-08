<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/8/22
 * Time: 4:30 PM
 */

namespace common\helper;


use common\helper\oss\XyfOss;
use yii\base\Model;

/**
 * 上传文件类
 * Class UploadFile
 * @package credit_api\helper
 */
class UploadFile extends Model
{
    /**
     * 获取规定的文件的相对路径
     * @param $filename
     * @param null|string|bool $app 为false时不加app前缀，做为公共文件件处理
     * @return string
     */
    public static function getRelativePath($filename, $app = null)
    {
        $pre = '';
        if ($app !== false) {
            $pre = ($app ?: Env::getApp()) . DIRECTORY_SEPARATOR;
        }
        return $pre . 'upload' . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * 获取文件url
     * @param $filename
     * @param null|string|bool $app 为false时不加app前缀，做为公共文件
     * @return bool|mixed
     * @throws \yii\base\Exception
     */
    public static function getUrl($filename, $app = null)
    {
        $ossClient = XyfOss::getInstance();
        $ret = $ossClient->getUrl(self::getRelativePath($filename, $app));
        if (!$ret) {
            throw $ossClient->error;
        }
        return $ret;
    }

    /**
     * 上传文件
     * @param $filename
     * @param $filePath
     * @param null|string|bool $app 为false时不加app前缀，做为公共文件
     * @return bool
     * @throws \yii\base\Exception
     */
    public static function uploadFile($filename, $filePath, $app = null)
    {
        $ossClient = XyfOss::getInstance();
        $ret = $ossClient->uploadFile(self::getRelativePath($filename, $app), $filePath);
        if (!$ret) {
            throw $ossClient->error;
        }
        return $ret;
    }

    /**
     * 下载文件
     * @param $filename
     * @param $filePath
     * @param null|string|bool $app 为false时不加app前缀，做为公共文件
     * @return bool
     * @throws \yii\base\Exception
     */
    public static function downloadFile($filename, $filePath, $app = null)
    {
        $ossClient = XyfOss::getInstance();
        $ret = $ossClient->downloadFile(self::getRelativePath($filename, $app), $filePath);
        if (!$ret) {
            throw $ossClient->error;
        }
        return $ret;
    }

    /**
     * 删除文件
     * @param $filename
     * @param null|string|bool $app 为false时不加app前缀，做为公共文件
     * @return bool
     * @throws \yii\base\Exception
     */
    public static function deleteFile($filename, $app = null)
    {
        $ossClient = XyfOss::getInstance();
        $ret = $ossClient->deleteFile(self::getRelativePath($filename, $app));
        if (!$ret) {
            throw $ossClient->error;
        }
        return $ret;
    }

    /**
     * 批量删除文件
     * @param $filenames
     * @param null|string|bool $app 为false时不加app前缀，做为公共文件
     * @return bool
     * @throws null
     */
    public static function deleteFiles($filenames, $app = null)
    {
        foreach ($filenames as &$filename) {
            $filename = self::getRelativePath($filename, $app);
        }
        $ossClient = XyfOss::getInstance();
        $ret = $ossClient->deleteFiles($filenames);
        if (!$ret) {
            throw $ossClient->error;
        }
        return $ret;
    }
}