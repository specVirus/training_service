<?php
namespace common\modules\api\models;

/**
 * Created by PhpStorm.
 * User: spec
 * Date: 27.10.16
 * Time: 13:44
 */
class User extends \common\modules\user\models\User
{
    const ERROR_CODE_LOGIN = 100;
    const ERROR_CODE_REGISTER = 101;
    const ERROR_CODE_CONFIRM_NOT_FOUND = 102;
    const ERROR_CODE_CONFIRM_IS_VERIFIED = 103;
    const ERROR_CODE_SAVE_USER = 104;
}