<?php

namespace frontend\components;

use common\models\User;
use common\models\UserService;
use yii\base\ErrorException;

class AuthClientHelper {
    public static $ServiceIdMapping = [
        'frontend\authclients\GitHub' => 'id',
        'yii\authclient\clients\YandexOpenId' => 'id',
        'yii\authclient\clients\Twitter' => 'id',
        'frontend\authclients\Facebook' => 'id',
        'frontend\authclients\VKontakte' => 'uid',
        'yii\authclient\clients\YandexOAuth' => 'id',
    ];
    /**
     * Finds service record for current logged client and returns corresponding user.
     * @param \yii\authclient\BaseClient $client AuthClient instance with social authenticated details(ie. user attributes)
     * @return User or null
     */
    public static function findUserByService(\yii\authclient\BaseClient $client)
    {
        $serviceType = $client->className();
        if (isset(static::$ServiceIdMapping[$client->className()])) {
            $id_attribute = static::$ServiceIdMapping[$client->className()];
            $attributes = $client->getUserAttributes();
            $serviceId = null;
            if (isset($attributes[$id_attribute])) {
                $serviceId = $attributes[$id_attribute];
            } else {
                throw new ErrorException("No user identified supplied by social service.");
            }
            $service = UserService::find()
                ->where([
                    'service_type' => $serviceType,
                    'service_id' => $serviceId,
                ])
                ->one();

            if ($service === null) {
                return null;
            }

            return $service->getUser();
        } else {
            throw new ErrorException("Unidentified social service used.");
        }

    }

    /**
     * Retrieves additional profile information which can be needed for first-login(registration)
     * and which was not provided by first api call.
     * Returns merged user attributes
     * @param \yii\authclient\BaseClient $client
     * @return BaseClient Client with merged attributes
     */
    public static function retrieveAdditionalData(\yii\authclient\BaseClient $client)
    {
        $attributes = $client->getUserAttributes();

        switch ($client->className()) {
            case 'frontend\authclients\GitHub':
                try {
                    $emails = $client->api('user/emails');

                    foreach ($emails as $email) {
                        if ($email['primary'] === true) {
                            $attributes['email'] = $email['email'];
                            break;
                        }
                    }

                } catch (\yii\authclient\InvalidResponseException $e) {
                    // no email :-
                }
                break;
            default:
                break;
        }
        $client->setUserAttributes($attributes);
        return $client;
    }


    /**
     * Converts service attributes to common\models\User model attributes
     * @param \yii\authclient\BaseClient $client
     * @return array Array of attributes by model type which we can apply by $model->setAttributes()
     */
    public static function mapUserAttributesWithService(\yii\authclient\BaseClient $client)
    {
        $mappings = [
            'service' => [
                // id of user in service
                'service_id' => static::$ServiceIdMapping,
            ],
            'user' => [
                'username' => [
                    'frontend\authclients\GitHub' => 'login',
                    'yii\authclient\clients\Twitter' => 'screen_name',
                    'frontend\authclients\VKontakte' => 'nickname',
                    'yii\authclient\clients\YandexOAuth' => 'login',
                ],
                'email' => [
                    'frontend\authclients\GitHub' => 'email',
                    'yii\authclient\clients\YandexOpenId' => 'email',
                    'frontend\authclients\Facebook' => 'email',
                    'yii\authclient\clients\YandexOAuth' => 'default_email',
                ],
                'name' => [
                    'frontend\authclients\GitHub' => 'name',
                    'yii\authclient\clients\YandexOpenId' => 'name',
                    'yii\authclient\clients\Twitter' => 'name',
                    'frontend\authclients\Facebook' => 'name',
                    'frontend\authclients\VKontakte' => ['first_name', 'last_name'],
                    'yii\authclient\clients\YandexOAuth' => ['first_name', 'last_name'],
                ],
                'avatar_url' => [
                    'frontend\authclients\GitHub' => 'avatar_url',
                    'yii\authclient\clients\Twitter' => 'profile_image_url',
                    'frontend\authclients\VKontakte' => 'photo',
                ],
                'company' => [
                    'frontend\authclients\GitHub' => 'company',
                ],
                'url' => [
                    'frontend\authclients\GitHub' => 'html_url',
                ],
                'location' => [
                    'frontend\authclients\GitHub' => 'location',
                ],
            ],
        ];

        $class_name = $client->className();
        $attributes = $client->getUserAttributes();
        $result = [];
        foreach ($mappings as $model_type => $mappings_by_attribute) {
            $result [$model_type] = [];

            foreach ($mappings_by_attribute as $attribute => $maps) {
                if (isset($maps[$class_name])) {
                    $key_in_attributes = $maps[$class_name];
                    $value = null;
                    if (is_array($key_in_attributes)) {
                        $value = [];
                        foreach ($key_in_attributes as $key) {
                            if (isset($attributes[$key])) {
                                $value[] = $attributes[$key];
                            }
                        }
                        if (count($value) > 0) {
                            $value = implode(' ', $value);
                        } else {
                            $value = null;
                        }
                    } else {
                        $value = isset($attributes[$key_in_attributes]) ? $attributes[$key_in_attributes] : null;
                    }

                    if ($value !== null) {
                        $result[$model_type][$attribute] = $value;
                    }
                }
            }
        }

        return $result;
    }

}

