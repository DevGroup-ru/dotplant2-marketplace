<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'name' => 'DotPlant2 CMS Marketplace',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'github' => [
                    'class' => 'frontend\authclients\GitHub',
                    'clientId' => 'ee323a17591ed603d8de',
                    'clientSecret' => '4a308e26483e436f075e35d6fc481189f572b161',
                ],
// Disabled due google oauth bug - can't make redirect url to local domain
//                'google' => [
//                    'class' => 'yii\authclient\clients\GoogleOAuth',
//                    'clientId' => '19769931080-g18dgtf6u7cnth5dsttgf08rruv0v3bi.apps.googleusercontent.com',
//                    'clientSecret' => 'MCud9uIzOwXULgqXyCSrpgiS',
//                ],
                'yandex' => [
                    'class' => 'yii\authclient\clients\YandexOpenId'
                ],
                'twitter' => [
                    'class' => 'yii\authclient\clients\Twitter',
                    'consumerKey' => '5GoINI8jObjcFGKELB8Qe0LmW',
                    'consumerSecret' => 'dvjsRQEWVIWDhkR9m4qThGYdWw3HWe6XX4jwPWx4u8u6aX1O0z',
                ],
                'facebook' => [
                    'class' => 'frontend\authclients\Facebook',
                    'clientId' => '1503116639959097',
                    'clientSecret' => '6585ac3ec461eb68ff450ce66816c91a',
                ],
                'yandex_oauth' => [
                    'class' => 'yii\authclient\clients\YandexOAuth',
                    'clientId' => '2e58aa528ea6429088a5ab4483dd3314',
                    'clientSecret' => '8eb40e112ffe424893955a1a3ed3886e',
                ],
// Disabled due VK doesn't give us an email
//                'vkontakte' => [
//                    'class' => 'frontend\authclients\VKontakte',
//                    'clientId' => '4640070',
//                    'clientSecret' => 'mnSJwL079Z9mCP6B6e55',
//                    'scope' => 'email',
//                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
            ],
        ],
    ],
    'params' => $params,
];
