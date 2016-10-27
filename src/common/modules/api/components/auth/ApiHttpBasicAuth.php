<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 18.05.16
 * Time: 14:00
 */

namespace common\modules\api\components\auth;

use Yii;
use yii\filters\auth\HttpBasicAuth;

/**
 * Class ApiHttpBasicAuth
 * @package common\modules\api\components\auth
 */
class ApiHttpBasicAuth extends HttpBasicAuth {
    const API_KEY = 'key';
    const API_PHONE = 'phone';

    /**
     * @inheritdoc
     * @param \common\modules\user\components\User $user
     * @param \yii\web\Request $request
     * @param \yii\web\Response $response
     * @return mixed|null|\yii\web\IdentityInterface
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function authenticate($user, $request, $response) {
        Yii::info(
            "UserIsGuest: " . Yii::$app->user->isGuest,
            'api/request'
        );
        $apiKey = $request->get(self::API_KEY);
        $apiPhone = $request->get(self::API_PHONE);
        if(empty($apiKey) || empty($apiPhone)) {
            $this->handleFailure($response);
            return null;
        }
        /** @var \common\modules\user\models\User $identity */
        $identity = $user->loginByAccessToken($apiKey, get_class($this));
        if($identity !== null && $identity->phone == $apiPhone) {
            return $identity;
        }
        $this->handleFailure($response);
        return null;
    }

    /**
     * @inheritdoc
     */
    public function challenge($response) {}
}