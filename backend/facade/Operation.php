<?php

namespace backend\facade;

use backend\models\BackendOperationRecord;
use common\facade\CommonSms;
use common\helper\Env;
use common\logging\DebugLog;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\web\Response;

/**
 * 操作日志
 */
class Operation
{
    public static function search($params)
    {
        $offset = max(0, $params['page'] - 1) * $params['page_size'];
        $query = BackendOperationRecord::find();
        if (!empty($params['start_time'])) {
            $query->andWhere(['>=', 'created_time', $params['start_time']]);
        }
        if (!empty($params['end_time'])) {
            $query->andWhere(['<=', 'created_time', $params['end_time']]);
        }
        if (!empty($params['ip'])) {
            $query->andWhere(['=', 'ip', $params['ip']]);
        }
        if (!empty($params['operator_name'])) {
            $query->andWhere(['=', 'operator_name', $params['operator_name']]);
        }
        if (!empty($params['operator_id'])) {
            $query->andWhere(['=', 'operator_id', $params['operator_id']]);
        }
        if (!empty($params['interface_name'])) {
            $query->andWhere(['=', 'interface_name', $params['interface_name']]);
        }
        if (!empty($params['interface_sign'])) {
            $query->andWhere(['=', 'interface_sign', $params['interface_sign']]);
        }
        $total = $query->count();
        $list = $query
            ->select([
                'menu',
                'interface_name',
                'interface_sign',
                'request',
                'response',
                'ip',
                'operator_id',
                'operator_name',
                'created_time',
            ])
            ->orderBy(['id' => SORT_DESC])
            ->offset($offset)
            ->limit($params['page_size'])
            ->asArray()
            ->all();
        return [
            'total' => $total,
            'list' => $list,
        ];
    }

    public static function log($result)
    {
        try {
            $headers = Yii::$app->request->getHeaders();
            $body = Yii::$app->request->getBodyParams();
            $user = User::getUser();
            $request = json_encode($body, JSON_UNESCAPED_UNICODE);

            if ($result instanceof Response) {
                $response = json_encode($result->data, JSON_UNESCAPED_UNICODE);
            } elseif (is_array($result)) {
                $response = json_encode($result, JSON_UNESCAPED_UNICODE);
            } else {
                $response = $result;
            }
            $interfaceSign = Yii::$app->request->getPathInfo();
            $interface = InterfaceNode::getBySign($interfaceSign);

            $menuId = $headers->get('menu-id');
            $menu = '无';
            if ($menuId) {
                $menu = Menu::getMenuFullName($menuId);
            }
            $operation = new BackendOperationRecord([
                'menu' => $menu,
                'interface_name' => $interface ? $interface->name : $interfaceSign,
                'interface_sign' => $interfaceSign,
                'request' => $request,
                'response' => $response,
                'ip' => Env::getUserRealIp(),
                'operator_id' => $user ? $user->id : 0,
                'operator_name' => $user ? $user->name : '系统',
            ]);
            $operation->msave();
        } catch (\Exception $e) {
            DebugLog::instance()->log((string)$e, 'operation_record_error');
            return false;
        }
        return true;
    }

    /**
     * 发送操作通知
     * @param $result
     * @throws InvalidConfigException
     * @throws UserException
     */
    public static function sendNotify($result)
    {
        if ($result instanceof Response) {
            $response = json_encode($result->data, JSON_UNESCAPED_UNICODE);
        } elseif (is_array($result)) {
            $response = json_encode($result, JSON_UNESCAPED_UNICODE);
        } else {
            $response = $result;
        }
        $user = User::getUser();
        $userName = $user ? $user->name : '未知';
        $interfaceSign = Yii::$app->request->getPathInfo();
        $interface = InterfaceNode::getBySign(Yii::$app->request->getPathInfo());
        $interfaceName = $interface ? $interface->name : $interfaceSign;

        $bizType = 'common_config_notify';
        $title = PROJECT_NAME_ZH . '后台配置变更[' . $userName . '][' . $interfaceName . ']' . date('Y-m-d H:i:s');
        $content = "## {$title} \n**参数**  \n```json \n" . json_encode(Yii::$app->request->getBodyParams(), JSON_UNESCAPED_UNICODE) . "\n```\n**响应**  \n```json\n" . $response . "\n```\n";

        CommonSms::instance()->sendNotify($title, $content, [], [$bizType], ['msg_type' => 'markdown']);
    }
}