<?php
/**
 * Created by PhpStorm.
 * User: spec
 * Date: 19.07.16
 * Time: 13:51
 */

namespace common\components\helpers;

/**
 * Class Json
 * @package common\components\helpers
 */
class Json extends \yii\helpers\Json {
    /**
     * @param $string
     * @return bool
     */
    public static function isJson($string) {
        if(!is_string($string)) {
            return false;
        }
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}