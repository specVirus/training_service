<?php
namespace common\modules\api\controllers\user\actions;

use yii\rest\Action;
use Yii;
use common\modules\api\forms\LoginForm;

/**
 * Created by PhpStorm.
 * User: spec
 * Date: 27.10.16
 * Time: 13:49
 */
class UserLoginAction extends Action
{
    public function run(){
        if (!Yii::$app->user->isGuest) {
            return Yii::$app->user->identity;
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            $model->login();
            $result =  Yii::$app->user->identity;
        } else {
            Yii::$app->getResponse()->setStatusCode(422, 'Fail validation');
            $result = [
                'result' => false,
                'message' => 'LABEL_REGISTER_ERROR',
                'errors' => $model->getErrors()
            ];
        }
        return $result;
    }
}