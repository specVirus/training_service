<?php
/**
 * Created by PhpStorm.
 * User: spec
 * Date: 04.04.16
 * Time: 14:34
 */

namespace common\modules\multilingual\widgets;

use yii\base\Widget;

/**
 * Class LanguageField
 * @package common\modules\multilingual\widgets
 */
class LanguageField extends Widget {
    public $attributes = [];
    public $form;
    public $model;
    public $useDefaultAttributes = true;

    public function run() {
        if(empty($this->form) || empty($this->model) || empty($this->attributes)) {
            return;
        }
        $items = [];
        foreach($this->model->languages as $language) {
            $items[] = [
                'label' => $language,
                'content' => $this->getLangContent($this->getLangAttribute($this->attributes, $language, $this->model->defaultLanguage)),
                'active' => $language == $this->model->defaultLanguage
            ];
        }
        return $this->render('lang-tabs', [
            'items' => $items
        ]);
    }

    protected function getLangAttribute($attributes, $language, $defaultLanguage) {
        $langPrefix = $language == $defaultLanguage && $this->useDefaultAttributes ? '' : '_' . $language;
        foreach($attributes as $key => $attribute) {
            $attributes[$key]['name'] = $attribute['name'] . $langPrefix;
            $attributes[$key]['attribute'] = $attribute['name'];
        }
        return $attributes;
    }

    protected function getLangContent($attributes, $html = null) {
        foreach($attributes as $attribute) {
            $attributeForm = $this->form
                ->field($this->model, $attribute['name'])
                ->label($this->model->getAttributeLabel($attribute['attribute']));

            if($attribute['type'] != 'widget') {
                $attributeForm->{$attribute['type']}(['maxlength' => true]);
            }else {
                $attributeForm->{$attribute['type']}($attribute['class'], [
                    'model' => $this->model,
                    'attribute' => $attribute['name']
                ]);
            }

            $html .= $attributeForm;
        }
        return $html;
    }
}