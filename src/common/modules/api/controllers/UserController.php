<?php

namespace common\modules\api\controllers;

use Yii;

/**
 * Default controller for the `api` module
 */
class UserController extends BaseApiActiveController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
