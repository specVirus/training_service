<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
    'modules' => [
        'api' => [
            'class' => 'common\modules\api\Module',
        ],
        'user' => [
            'class' => 'common\modules\user\Module',
        ],
    ],
];
