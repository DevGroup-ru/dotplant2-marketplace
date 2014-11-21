<?php

namespace common\models;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_service".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $service_type
 * @property string $service_id
 */
class UserService extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_service}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['service_type', 'service_id', 'user_id'], 'required'],
            [
                ['service_type', 'service_id'],
                'unique',
                'targetAttribute' => ['service_type', 'service_id'],
                'message' => 'The combination of Service Type and Service ID has already been taken.'
            ],
        ];
    }

    public function scenarios()
    {
        return [
            'default' => ['user_id', 'service_type', 'service_id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'service_type' => 'Service Type',
            'service_id' => 'Service ID',
        ];
    }

    public function getUser()
    {
        return User::findById($this->user_id);
    }


}
