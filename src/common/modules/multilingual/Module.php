<?php

namespace common\modules\multilingual;

/**
 * multilingual module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $languages = [
        'ru' => 'Russian',
        'en-US' => 'English',
    ];
    public $defaultLanguage = 'en';
    public $controllerNamespace = 'common\modules\multilingual\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
