<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.03.16
 * Time: 12:57
 */

namespace common\components\helpers;

/**
 * Class StringHelper
 * @package common\components\helpers
 */
class StringHelper {
    /**
     * @param $string
     * @param string $encoding
     * @return string
     */
    public static function mbUcfirst($string, $encoding = 'UTF-8') {
        $string = mb_ereg_replace('^[\ ]+', '', $string);
        $first = mb_strtoupper(mb_substr($string, 0, 1, $encoding), $encoding);
        $last = mb_strtolower(mb_substr($string, 1, mb_strlen($string), $encoding));
        $string = $first . $last;
        return $string;
    }

    /**
     * @param $string
     * @param $count
     * @return string
     */
    public static function cutString($string, $count) {
        if(strlen($string) <= $count) {
            return $string;
        }
        $cutString = strip_tags($string);
        $cutString = substr($cutString, 0, $count);
        $cutString = rtrim($cutString, "!,.-");
        $cutString = substr($cutString, 0, strrpos($cutString, ' '));
        $cutString = $cutString . '...';
        return $cutString;
    }

    /**
     * @param $money
     * @return mixed
     */
    public static function decimalFormat($money) {
        return str_replace('.00', '', $money);
    }

    /**
     * @param $errors
     * @return string
     */
    public static function getModelStringErrors($errors) {
        if(empty($errors)) {
            return '';
        }
        if(is_string($errors)) {
            return $errors;
        }
        $errorsArray = [];
        foreach((array)$errors as $attribute => $error) {
            foreach($error as $item) {
                $errorsArray[] = $item;
            }
        }
        return implode('. ', $errorsArray);
    }
}