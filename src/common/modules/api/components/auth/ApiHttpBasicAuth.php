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
        if(empty($apiKey)) {
            $this->handleFailure($response);
            return null;
        }
        if($this->auth) {
            if($apiKey !== null) {
                $identity = call_user_func($this->auth, $apiKey);
                if($identity !== null) {
                    $user->switchIdentity($identity);
                }else {
                    $this->handleFailure($response);
                }

                return $identity;
            }
        }else{
            /** @var \common\modules\user\models\User $identity */
            $identity = $user->loginByAccessToken($apiKey, get_class($this));
            if($identity !== null) {
                return $identity;
            }
            $this->handleFailure($response);
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function challenge($response) {}
}