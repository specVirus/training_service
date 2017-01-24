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
    public function run()
    {
        $model = new RegisterForm();
        $result = ['error_code' => User::ERROR_CODE_REGISTER];
        if($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            /** @var User $user */
            if($user = $model->signup()) {
                $this->afterRegister($user);
                $result = ['api_key' => $user->api_key];
            }
        }else {
            Yii::$app->getResponse()->setStatusCode(422, 'Fail validation');
        }

        return $result;
    }

    /**
     * Process data after registration
     *
     * @param User $user
     */
    protected function afterRegister($user)
    {
        $message = Yii::t('app', 'Код активации: ' . $user->confirmation_phone_code);
        Yii::$app->sms->send($user->phone, $message);
    }
}