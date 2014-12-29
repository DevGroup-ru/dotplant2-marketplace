<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%category_lang}}".
 *
 * @property integer $id
 * @property string $category_id
 * @property string $language
 * @property string $name
 * @property string $description
 */
class CategoryLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category_lang}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id'], 'integer'],
            [['language', 'name'], 'required'],
            [['description'], 'default', 'value' => ''],
            [['description'], 'string'],
            [['language'], 'string', 'max' => 5],
            [['name'], 'string', 'max' => 255],
            [['language', 'category_id'], 'unique', 'targetAttribute' => ['language', 'category_id'], 'message' => 'The combination of Category ID and Language has already been taken(translation already exists).']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category_id' => Yii::t('app', 'Category ID'),
            'language' => Yii::t('app', 'Language'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
}
