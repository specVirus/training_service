<?php

namespace common\modules\api;

use common\modules\api\controllers\user\UserController;

/**
 * api module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'common\modules\api\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->controllerMap = [
            'user' => UserController::className(),
        ];
    }
}
