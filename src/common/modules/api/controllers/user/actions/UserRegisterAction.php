<?php
/**
 * Created by PhpStorm.
 * User: spec
 * Date: 27.10.16
 * Time: 13:49
 */

namespace common\modules\api\controllers\user\actions;

use common\modules\api\forms\RegisterForm;
use common\modules\api\models\User;
use yii\rest\Action;
use Yii;

class UserRegisterAction extends Action
{
    public function run(){
        $result = [
            'result' => false,
            'message' => 'LABEL_REGISTER_ERROR',
            'errors' => []
        ];
        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            /** @var User $user */
            if ($user = $model->signup()) {
                $message = 'LABEL_SUCCESSFULLY_REGISTERED';
                $this->afterRegister($user);
                if(Yii::$app->user->isGuest) {
                    $message = 'LABEL_SUCCESSFULLY_REGISTERED_PLEASE_CHECK_YOUR_PHONE';
                }
                $result = [
                    'result' => true,
                    'message' => $message,
                    'errors' => []
                ];
            }
        }else {
            Yii::$app->getResponse()->setStatusCode(422, 'Fail validation');
            $result = [
                'result' => false,
                'message' => 'LABEL_REGISTER_ERROR',
                'errors' => $model->getErrors()
            ];
        }
        return $result;
    }

    /**
     * Process data after registration
     * @param User $user
     */
    protected function afterRegister($user) {
        $message = Yii::t('app', 'Код активации: '.$user->confirmation_phone_code);
        Yii::$app->sms->send($user->phone, $message);
    }
}