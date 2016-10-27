<?php

namespace common\modules\multilingual\models;

use common\models\BaseModel;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "translation".
 *
 * @property integer $id
 * @property string $entity
 * @property integer $entity_id
 * @property string $data
 * @property string $language
 */
class Translation extends BaseModel {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'translation';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['entity', 'entity_id'], 'required'],
            [['entity_id'], 'integer'],
            [['data'], 'string'],
            [['entity', 'language'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'entity' => Yii::t('app', 'Entity'),
            'entity_id' => Yii::t('app', 'Entity ID'),
            'data' => Yii::t('app', 'Data'),
            'language' => Yii::t('app', 'Language'),
        ];
    }

    /**
     * @param $attributeName
     * @return null
     */
    public function getAttributeValue($attributeName) {
        $data = Json::decode($this->data);
        if(!is_array($data) || !isset($data[$attributeName])) {
            return null;
        }
        return $data[$attributeName];
    }
}
