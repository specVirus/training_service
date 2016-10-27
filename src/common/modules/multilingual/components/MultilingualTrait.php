<?php
namespace common\modules\multilingual\components;

use common\models\BaseModel;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\VarDumper;

/**
 * Multilingual trait.
 * Modify ActiveRecord query for multilingual support
 * @package common\modules\multilingual\components
 */
trait MultilingualTrait {
    /**
     * @var string the name of the lang field of the translation table. Default to 'language'.
     */
    public $languageField = 'language';

    /**
     * Scope for querying by languages
     * @param $language
     * @param $abridge
     * @return $this
     */
    public function localized($language = null, $abridge = true) {
        if(!$language)
            $language = Yii::$app->language;

        if(!isset($this->with['translation'])) {
            $this->with(['translation' => function ($query) use ($language, $abridge) {
                $query->andWhere([$this->languageField => $abridge ? substr($language, 0, 2) : $language]);
            }]);
        }

        return $this;
    }

    /**
     * Scope for querying by all languages
     * @return $this
     */
    public function multilingual() {
        if(isset($this->with['translation'])) {
            unset($this->with['translation']);
        }
        $this->with('translation');
        return $this;
    }

    /**
     * Scope for querying by all languages
     * @return $this
     */
    public function langSearch() {
        $this->with('translation');
        $this->joinWith(['translation']);
        return $this;
    }

    /**
     * @param $attribute
     * @param $value
     * @param $operator
     * @param string $alias
     * @return $this
     */
    public function andLangWhere($attribute, $value, $operator, $alias = 'translation') {
        if(empty($value)) {
            return $this;
        }
        $condition = $this->filterCondition(['or', $this->getLangCondition($operator, $attribute, $value, $alias)]);
        if($condition !== []) {
            $this->andWhere($condition);
        }
        return $this;
    }

    /**
     * @param $attribute
     * @param $value
     * @param $operator
     * @param string $alias
     * @return $this
     */
    public function orLangWhere($attribute, $value, $operator, $alias = 'translation') {
        if(empty($value)) {
            return $this;
        }
        $condition = $this->filterCondition(['or', $this->getLangCondition($operator, $attribute, $value, $alias)]);
        if($condition !== []) {
            $this->orWhere($condition);
        }
        return $this;
    }

    /**
     * @param $operator
     * @param $attribute
     * @param $value
     * @param $alias
     * @return string
     */
    protected function getLangCondition($operator, $attribute, $value, $alias) {
        $value = str_replace("'", '"', $value);
        if($operator == 'like') {
            $value = "%$value%";
        }
        if(strpos($attribute, '(') !== false) {
            $attributeArray = explode('(', $attribute);
            $attribute = substr($attributeArray[1], 0, -1);
            $function = $attributeArray[0];
        }
        if(empty($function)) {
            return $alias . ".data ->> '" . $attribute . "' " . $operator . " '" . $value . "'";
        }
        return $function . "(" . $alias . ".data ->> '" . $attribute . "') " .  $operator . " '" . $value . "'";
    }

    /**
     * @param $query
     * @param $alias
     * @return mixed
     */
    public static function editTranslationWhere($query, $alias) {
        foreach($query->where as $attr => $val) {
            $newAttr = str_replace('translation', $alias, $attr);
            $query->where[$newAttr] = $val;
            unset($query->where[$attr]);
        }
        return $query;
    }

    /**
     * @param null $db
     * @return mixed
     * @throws \Exception
     */
    public function one($db = null) {
        $yiiDb = Yii::$app->db;
        $result = $yiiDb->cache(function ($yiiDb) use ($db) {
            return parent::one($db);
        }, BaseModel::CACHE_DURATION);
        return $result;
    }

    /**
     * @param null $db
     * @return mixed
     * @throws \Exception
     */
    public function all($db = null) {
        $yiiDb = Yii::$app->db;
        $result = $yiiDb->cache(function ($yiiDb) use ($db) {
            return parent::all($db);
        }, BaseModel::CACHE_DURATION);
        return $result;
    }
}
