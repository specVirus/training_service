<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 03.02.16
 * Time: 16:06
 */

namespace common\components\helpers;

/**
 * Class DateHelper
 * @package common\components\helpers
 */
class JsonbHelper {

    /**
     * @param $array
     * @return string
     */
    public static function getSqlStringArray($array) {
        $stringArray = null;
        foreach ($array as $row){
            if(empty($stringArray)){
                $stringArray = "'$row'";
                continue;
            }
            $stringArray .= ", '$row'";
        }

        return "array[$stringArray]";
    }

    /**
     * @param $column
     * @param $value
     * @return string
     */
    public static function getWhereJsonbExistsAll($column, $value) {
        $stringArray = self::getSqlStringArray(is_array($value) ? $value : [$value]);
        return "jsonb_exists_all($column, $stringArray)";
    }
}