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
        if ($model->load(Yii::$app->request->post(), '') && $model->login()) {
            return Yii::$app->user->identity;
        } else {
            return false;
        }
    }
}