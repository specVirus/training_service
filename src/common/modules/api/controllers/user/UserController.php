<?php

namespace common\modules\api\controllers\user;

use common\modules\api\controllers\BaseApiActiveController;
use common\modules\api\controllers\user\actions\UserConfirmAction;
use common\modules\api\controllers\user\actions\UserLoginAction;
use common\modules\api\controllers\user\actions\UserRegisterAction;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * User controller for the `api` module
 */
class UserController extends BaseApiActiveController
{
    /**
     * @var string
     */
    public $modelClass = 'common\modules\api\models\User';

    /**
     * @return array
     */
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
            'login',
            'register',
            'confirm',
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => [
                        'login',
                        'register',
                        'confirm',
                    ],
                    'allow' => true,
                    'roles' => ['?'],
                ],
            ],
        ];

        return $behaviors;
    }

    /**
     * Declares the allowed HTTP verbs.
     * Please refer to [[VerbFilter::actions]] on how to declare the allowed verbs.
     * @return array the allowed HTTP verbs.
     */
    protected function verbs() {
        return ArrayHelper::merge(parent::verbs(), [
            'login' => ['POST'],
            'register' => ['POST'],
        ]);
    }

    /**
     * @return array
     */
    public function actions() {
        return array_merge(parent::actions(), [
            'login' => [
                'class' => UserLoginAction::className(),
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'register' => [
                'class' => UserRegisterAction::className(),
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'confirm' => [
                'class' => UserConfirmAction::className(),
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
        ]);
    }
}
