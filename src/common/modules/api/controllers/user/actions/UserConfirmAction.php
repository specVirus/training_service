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
    public function run(){
        $postData = Yii::$app->request->post();
        if(empty($postData['code']) || empty($postData['phone'])){
            throw new BadRequestHttpException();
        }
        $user = User::findByCodePhone($postData['phone'], $postData['code']);
        if(empty($user)){
            throw new NotFoundHttpException(Yii::t('app', 'LABEL_ERROR_USER_DOES_NOT_EXIST'));
        }
        if($user->is_verified_phone){
            throw new Exception(Yii::t('app', 'LABEL_ERROR_USER_IS_VERIFIED'));
        }
        $user->status = User::STATUS_ACTIVE;
        $user->is_verified_phone = true;
        return $user->save();
    }
}