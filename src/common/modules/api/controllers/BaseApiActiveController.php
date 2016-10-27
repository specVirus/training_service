<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.05.16
 * Time: 13:54
 */

namespace common\modules\api\controllers;

use Yii;
use yii\base\Event;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\Response;
use common\modules\api\components\auth\ApiHttpBasicAuth;

/**
 * Class BaseApiActiveController
 * @package common\modules\api\controllers
 */
class BaseApiActiveController extends ActiveController {
    const EVENT_INIT_BASE_API_CONTROLLER = 'eventInitBaseApiController';

    /**
     * @var string
     */
    public $format = Response::FORMAT_JSON;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init() {
        parent::init();
        $event = new Event();
        $this->trigger(self::EVENT_INIT_BASE_API_CONTROLLER, $event);
    }

    /**
     * @return array
     */
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = $this->format;
        $behaviors['authenticator']['class'] = ApiHttpBasicAuth::className();
        $behaviors['authenticator']['auth'] = function ($username, $password) {
            if(!Yii::$app->user->isGuest) {
                return Yii::$app->user->identity;
            }
        };
        $behaviors = ArrayHelper::merge([
            'corsFilter' => [
                'class' => Cors::className(),
                #common rules
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['POST', 'PUT', 'GET', 'DELETE', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => null,
                    'Access-Control-Max-Age' => 0,
                    'Access-Control-Expose-Headers' => ['*'],
                ]
            ],
        ], $behaviors);
        return $behaviors;
    }

    /**
     * Declares the allowed HTTP verbs.
     * Please refer to [[VerbFilter::actions]] on how to declare the allowed verbs.
     * @return array the allowed HTTP verbs.
     */
    protected function verbs() {
        return [
            '' => ['OPTIONS'],
        ];
    }
}