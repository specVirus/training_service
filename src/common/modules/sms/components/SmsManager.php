<?php
namespace common\modules\sms\components;
/**
 * Created by PhpStorm.
 * User: spec
 * Date: 03.11.16
 * Time: 13:38
 */

use yii\base\Component;
use common\modules\sms\models\SmsLog;

class SmsManager extends Component
{
    public function send($phone, $message){
        $send = true;
        /**
         * todo отправка SMS
         */
        if($send){
            $this->logging($phone, $message);
        }
        return $send;
    }

    protected function logging($phone, $message){
        $smsLog = new SmsLog();
        $smsLog->message = $message;
        $smsLog->phone = $phone;
        $smsLog->save(false);
    }
}