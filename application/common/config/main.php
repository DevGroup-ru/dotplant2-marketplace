<?php
return [
    'name' => 'DotPlant2 CMS Marketplace',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager' => [
            'class'=>'\bethrezen\CachedDbManager',
        ],
    ],
];
