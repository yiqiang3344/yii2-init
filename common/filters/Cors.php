<?php

namespace common\filters;

use yiqiang3344\yii2_lib\helper\exception\OptionsException;
use yii\base\ActionFilter;
use \Yii;

/**
 * 跨域
 */
class Cors extends ActionFilter
{
    public $cors = [
        'Origin' => ['*'],
        'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
        'Access-Control-Allow-Headers' => ['*'],
        'Access-Control-Allow-Credentials' => null,
    ];

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();

        $requestOrigin = $request->headers->get('Origin');
        $origin = '';
        if (empty($this->cors['Origin']) || in_array('*', $this->cors['Origin']) || in_array($requestOrigin, $this->cors['Origin'])) {
            $origin = $requestOrigin;
        }
        $method = implode(',', $this->cors['Access-Control-Request-Method'] ?? []);
        $header = implode(',', $this->cors['Access-Control-Allow-Headers'] ?? []);

        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Request-Method', $method);
        $response->headers->set('Access-Control-Allow-Headers', $header);

        if (!empty($this->cors['Access-Control-Allow-Credentials'])) {
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }

        if ($request->isOptions) {
            throw new OptionsException();
        }
        return parent::beforeAction($action);
    }
}