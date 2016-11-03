<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class' => 'common\modules\user\components\User',
        ],
        'sms' => [
            'class' => 'common\modules\sms\components\SmsManager',
        ],
    ],
    'modules' => [
        'api' => [
            'class' => 'common\modules\api\Module',
        ],
        'user' => [
            'class' => 'common\modules\user\Module',
        ],
        'sms' => [
            'class' => 'common\modules\sms\Module',
        ],
    ],
];
