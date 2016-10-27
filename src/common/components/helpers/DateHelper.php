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
class DateHelper {
    /**
     * @param $date
     * @return bool|string
     */
    public static function rusFormat($date) {
        return
            date('d', strtotime($date))
            . ' '
            . self::monthTranslate(date('m', strtotime($date)))
            . ' '
            . date('Y', strtotime($date));
    }

    /**
     * @param $date
     * @return string
     */
    public static function dateFormat($date) {
        return date('d.m.Y', strtotime($date));
    }

    /**
     * @param $month
     * @return mixed
     */
    public static function monthTranslate($month) {
        $translate = [
            '01' => 'Января',
            '02' => 'Февраля',
            '03' => 'Марта',
            '04' => 'Апреля',
            '05' => 'Мая',
            '06' => 'Июня',
            '07' => 'Июля',
            '08' => 'Августа',
            '09' => 'Сентября',
            '10' => 'Октября',
            '11' => 'Ноября',
            '12' => 'Декабря'
        ];
        if(isset($translate[$month])) {
            return $translate[$month];
        }
    }
}