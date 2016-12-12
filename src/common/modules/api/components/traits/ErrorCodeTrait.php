<?php

namespace common\modules\api\components\traits;
use yii\base\Model;


/**
 * Trait RoleOwnerTrait
 * @package app\modules\pasport\components\traits
 *
 */
trait ErrorCodeTrait {
    public function getErrorCode(){
        /**
         * @var $this Model
         */
        var_dump($this->getErrors());die;
    }
}