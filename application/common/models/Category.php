<?php

namespace common\models;

use common\traits\FindById;
use dosamigos\translateable\TranslateableBehavior;
use Yii;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property integer $id
 * @property string $parent_id
 * @property string $slug
 * @property string $sort_order
 */
class Category extends \yii\db\ActiveRecord
{
    use FindById;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'sort_order'], 'integer'],
            [['slug'], 'required'],
            [['slug'], 'string', 'max' => 120],
            [['name', 'description'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'slug' => Yii::t('app', 'Slug'),
            'sort_order' => Yii::t('app', 'Sort Order'),
        ];
    }

    public function behaviors()
    {
        return [
            'trans' => [
                'class' => TranslateableBehavior::className(),
                'translationAttributes' => [
                    'name', 'description'
                ]
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(CategoryLang::className(), ['category_id' => 'id']);
    }
}
