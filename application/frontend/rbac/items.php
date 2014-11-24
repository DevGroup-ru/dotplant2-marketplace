<?php
return [
    'manageCategories' => [
        'type' => 2,
        'description' => 'Manage categories',
    ],
    'moderateItems' => [
        'type' => 2,
        'description' => 'Moderate items',
    ],
    'translateItems' => [
        'type' => 2,
        'description' => 'Translate items',
    ],
    'moderateTags' => [
        'type' => 2,
        'description' => 'Moderate tags',
    ],
    'translateCategories' => [
        'type' => 2,
        'description' => 'Translate categories',
    ],
    'moderator' => [
        'type' => 1,
        'children' => [
            'moderateItems',
            'moderateTags',
        ],
    ],
    'translator' => [
        'type' => 1,
        'children' => [
            'translateItems',
            'translateCategories',
        ],
    ],
    'admin' => [
        'type' => 1,
        'children' => [
            'manageCategories',
            'moderator',
            'translator',
        ],
    ],
];
