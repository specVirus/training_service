<?php
namespace common\modules\multilingual\components;

use Yii;
use yii\base\Behavior;
use yii\base\UnknownPropertyException;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;
use \common\components\helpers\Json;
use yii\validators\Validator;

/**
 * Class MultilingualBehavior
 * @package common\modules\multilingual\components
 */
class MultilingualBehavior extends Behavior {
    const SCENARIO_UPDATE_LANG = 'updateLang';

    /**
     * Multilingual attributes
     * @var array
     */
    public $attributes;

    /**
     * @var array
     */
    public $relationLangMap = [];

    /**
     * Available languages
     * It can be a simple array: array('fr', 'en') or an associative array: array('fr' => 'Français', 'en' => 'English')
     * For associative arrays, only the keys will be used.
     * @var array
     */
    public $languages;

    /**
     * @var string the default language.
     * Example: 'en'.
     */
    public $defaultLanguage;

    /**
     * @var string the prefix of the localized attributes in the lang table. Here to avoid collisions in queries.
     * In the translation table, the columns corresponding to the localized attributes have to be name like this: 'l_[name of the attribute]'
     * and the id column (primary key) like this : 'l_id'
     * Default to ''.
     */
    public $localizedPrefix = '';

    /**
     * @var boolean if this property is set to true required validators will be applied to all translation models.
     * Default to false.
     */
    public $requireTranslations = false;

    /**
     * @var boolean whether to abridge the language ID.
     * Default to true.
     */
    public $abridge = true;

    /**
     * @var
     */
    public $currentLanguage;
    private $ownerClassName;
    private $ownerPrimaryKey;
    private $ownerClassShortName;

    /**
     * @var array
     */
    private $langAttributes = [];

    /**
     * @var array excluded validators
     */
    private $excludedValidators = ['unique'];

    /**
     * @inheritdoc
     */
    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attach($owner) {
        /** @var ActiveRecord $owner */
        parent::attach($owner);
        $module = Yii::$app->getModule('multilingual');
        if(empty($this->languages) || !is_array($this->languages)) {
            $this->languages = $module->languages;
        }
        if(empty($this->languages) || !is_array($this->languages)) {
            throw new InvalidConfigException('Please specify array of available languages for the ' . get_class($this) . ' in the '
                . get_class($this->owner) . ' or in the application parameters', 101);
        }
        if(array_values($this->languages) !== $this->languages) { //associative array
            $this->languages = array_keys($this->languages);
        }
        $this->languages = array_unique(array_map(function ($language) {
            return $this->getLanguageBaseName($language);
        }, $this->languages));
        if(!$this->defaultLanguage) {
            if(empty($module->defaultLanguage)) {
                $this->defaultLanguage = isset(Yii::$app->params['defaultLanguage']) && Yii::$app->params['defaultLanguage'] ?
                    Yii::$app->params['defaultLanguage'] : Yii::$app->language;
            }else {
                $this->defaultLanguage = $module->defaultLanguage;
            }
        }
        $this->defaultLanguage = $this->getLanguageBaseName($this->defaultLanguage);
        if(!$this->currentLanguage) {
            $this->currentLanguage = $this->getLanguageBaseName(Yii::$app->language);
        }
        if(empty($this->attributes) || !is_array($this->attributes)) {
            throw new InvalidConfigException('Please specify multilingual attributes for the ' . get_class($this) . ' in the '
                . get_class($this->owner), 103);
        }
        $this->ownerClassName = get_class($this->owner);
        $this->ownerClassShortName = $this->getShortClassName($this->ownerClassName);
        /** @var ActiveRecord $className */
        $className = $this->ownerClassName;
        $this->ownerPrimaryKey = $className::primaryKey()[0];
        $rules = $owner->rules();
        $validators = $owner->getValidators();
        foreach($rules as $rule) {
            if(in_array($rule[1], $this->excludedValidators))
                continue;
            $rule_attributes = is_array($rule[0]) ? $rule[0] : [$rule[0]];
            $attributes = array_intersect($this->attributes, $rule_attributes);
            if(empty($attributes))
                continue;
            $rule_attributes = [];
            foreach($attributes as $key => $attribute) {
                foreach($this->languages as $language)
                    if($language != $this->defaultLanguage)
                        $rule_attributes[] = $this->getAttributeName($attribute, $language);
            }
            if(isset($rule['skipOnEmpty']) && !$rule['skipOnEmpty'])
                $rule['skipOnEmpty'] = !$this->requireTranslations;
            $params = array_slice($rule, 2);

            if($rule[1] !== 'required' || $this->requireTranslations) {
                $validators[] = Validator::createValidator($rule[1], $owner, $rule_attributes, $params);
            }elseif($rule[1] === 'required') {
                $validators[] = Validator::createValidator('safe', $owner, $rule_attributes, $params);
            }
        }
        foreach($this->languages as $lang) {
            foreach($this->attributes as $attribute) {
                $attributeName = $this->localizedPrefix . $attribute;
                $this->setLangAttribute($this->getAttributeName($attribute, $lang), $this->getAttributeValue($attributeName));
                if($lang == $this->defaultLanguage) {
                    $this->setLangAttribute($attribute, $this->getAttributeValue($attributeName));
                }
            }
        }
    }

    public function getAttributeValue($attributeName) {
        $data = Json::decode($this->owner->$attributeName);
        if(!is_array($data) || !isset($data[$attributeName])) {
            return null;
        }
        return $data[$attributeName];
    }

    /**
     * Handle 'beforeValidate' event of the owner.
     */
    public function beforeValidate() {
        foreach($this->attributes as $attribute) {
            $this->setLangAttribute($attribute, $this->getLangAttribute($attribute));
        }
    }

    protected function initTranslations() {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        $currentLang = $this->getCurrentLanguage();
        if($owner->scenario == self::SCENARIO_UPDATE_LANG){
            $currentLang = $this->defaultLanguage;
        }
        foreach($this->attributes as $attribute) {
            if(!Json::isJson($owner->$attribute)){
                continue;
            }
            $attributeLangArray = Json::decode($owner->$attribute);
            if(!is_array($attributeLangArray)){
                continue;
            }
            foreach($attributeLangArray as $attributeLang => $value){
                $this->setLangAttribute($this->getAttributeName($attribute, $attributeLang), $value);
                if($attributeLang == $currentLang) {
                    $this->setLangAttribute($attribute, $value);
                }
            }
        }
    }

    /**
     * Handle 'afterFind' event of the owner.
     */
    public function afterFind() {
        $this->initTranslations();
        $this->initTranslationAttributes();
    }

    protected function initTranslationAttributes() {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        foreach($this->attributes as $attribute) {
            if($owner->hasAttribute($attribute)) {
                $owner->setAttribute($attribute, $this->getLangAttribute($attribute));
            }elseif(isset($owner->$attribute)) {
                $owner->$attribute = $this->getLangAttribute($attribute);
            }
        }
    }

    /**
     * Handle 'beforeInsert' event of the owner.
     */
    public function beforeInsert() {
        $this->saveTranslations();
    }

    /**
     * Handle 'beforeUpdate' event of the owner.
     */
    public function beforeUpdate() {
        $this->saveTranslations();
    }

    /**
     *
     */
    private function saveTranslations() {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        foreach($this->attributes as $attribute) {
            $arrayValues = [];
            foreach($this->languages as $lang) {
                $value = $this->getLangAttribute($this->getAttributeName($attribute, $lang));
                if($value !== null) {
                    $value = str_replace("'", '"', $value);
                    $arrayValues[$lang] = (string)$value;
                }
            }
            $owner->$attribute = Json::encode($arrayValues);
        }
    }

    /**
     * @return null
     */
    protected function getEntity() {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        $schema = $owner->getTableSchema();
        if(empty($schema)) {
            return null;
        }
        return $schema->name;
    }

    /**
     * @inheritdoc
     * @param string $name
     * @param bool|true $checkVars
     * @return bool
     */
    public function canGetProperty($name, $checkVars = true) {
        return method_exists($this, 'get' . $name) || $checkVars && property_exists($this, $name)
        || $this->hasLangAttribute($name);
    }

    /**
     * @inheritdoc
     * @param string $name
     * @param bool|true $checkVars
     * @return bool
     */
    public function canSetProperty($name, $checkVars = true) {
        return $this->hasLangAttribute($name);
    }

    /**
     * @inheritdoc
     * @param string $name
     * @return mixed|string
     * @throws UnknownPropertyException
     * @throws \Exception
     */
    public function __get($name) {
        try {
            return parent::__get($name);
        }catch(UnknownPropertyException $e) {
            if($this->hasLangAttribute($name)) return $this->getLangAttribute($name);
            // @codeCoverageIgnoreStart
            else throw $e;
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * @inheritdoc
     * @param string $name
     * @param mixed $value
     * @throws UnknownPropertyException
     * @throws \Exception
     */
    public function __set($name, $value) {
        try {
            parent::__set($name, $value);
        }catch(UnknownPropertyException $e) {
            if($this->hasLangAttribute($name)) $this->setLangAttribute($name, $value);
            // @codeCoverageIgnoreStart
            else throw $e;
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        if(!parent::__isset($name)) {
            return $this->hasLangAttribute($name);
        }else {
            return true;
        }
    }

    /**
     * Whether an attribute exists
     * @param string $name the name of the attribute
     * @return boolean
     */
    public function hasLangAttribute($name) {
        return array_key_exists($name, $this->langAttributes);
    }

    /**
     * @param string $name the name of the attribute
     * @return string the attribute value
     */
    public function getLangAttribute($name) {
        return $this->hasLangAttribute($name) ? $this->langAttributes[$name] : null;
    }

    /**
     * @param string $name the name of the attribute
     * @param string $value the value of the attribute
     */
    public function setLangAttribute($name, $value) {
        $value = str_replace('"', "'", $value);
        $this->langAttributes[$name] = $value;
    }

    /**
     * @return array
     */
    public function getLangAttributes() {
        return $this->langAttributes;
    }

    /**
     * @param $language
     * @return string
     */
    protected function getLanguageBaseName($language) {
        return $this->abridge ? substr($language, 0, 2) : $language;
    }

    /**
     * @param string $className
     * @return string
     */
    private function getShortClassName($className) {
        return substr($className, strrpos($className, '\\') + 1);
    }

    /**
     * @return mixed|string
     */
    public function getCurrentLanguage() {
        return $this->currentLanguage;
    }

    /**
     * @param $attribute
     * @param $language
     * @return string
     */
    protected function getAttributeName($attribute, $language) {
        $language = $this->abridge ? $language : Inflector::camel2id(Inflector::id2camel($language), "_");
        return $attribute . "_" . $language;
    }

    /**
     * @param $attributes
     */
    public function setLangAttributes($attributes) {
        foreach($attributes as $name => $value) {
            $this->setLangAttribute($name, $value);
        }
        $this->initTranslationAttributes();
    }
}