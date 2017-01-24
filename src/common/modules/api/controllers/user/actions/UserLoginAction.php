<?php
namespace common\modules\api\controllers\user\actions;

use common\modules\api\models\User;
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
    public function run()
    {
        $model = new LoginForm();
        if($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            $model->login();
            /** @var User $identity */
            $identity = Yii::$app->user->identity;
            $result = ['api_key' => $identity->api_key];
        }else {
            Yii::$app->getResponse()->setStatusCode(422, 'Fail validation');
            $result = ['error_code' => User::ERROR_CODE_LOGIN];
        }

        return $result;
    }
}