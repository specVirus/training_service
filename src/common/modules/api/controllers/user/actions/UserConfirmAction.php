<?php
/**
 * Created by PhpStorm.
 * User: spec
 * Date: 27.10.16
 * Time: 13:50
 */

namespace common\modules\api\controllers\user\actions;

use common\modules\api\models\User;
use yii\base\Exception;
use yii\db\Expression;
use yii\rest\Action;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class UserConfirmAction extends Action
{
    public function run()
    {
        $postData = Yii::$app->request->post();
        if(empty($postData['code']) || empty($postData['phone'])) {
            throw new BadRequestHttpException();
        }
        /** @var User $user */
        $user = User::findByCodePhone($postData['phone'], $postData['code']);
        if(empty($user)) {
            return ['error_code' => User::ERROR_CODE_CONFIRM_NOT_FOUND];
        }
        if($user->is_verified_phone) {
            return ['error_code' => User::ERROR_CODE_CONFIRM_IS_VERIFIED];
        }
        $user->status = User::STATUS_ACTIVE;
        $user->is_verified_phone = true;
        if($user->save()) {
            return ['api_key' => $user->api_key];
        }

        return ['error_code' => User::ERROR_CODE_SAVE_USER];
    }
}