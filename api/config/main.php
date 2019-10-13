<?php

use scl\yii\tools\components\ApiErrorHandler;
use yii\filters\ContentNegotiator;
use yii\web\Response;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => [
        'log',
        [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
            'languages' => ['ru', 'en']
        ],
    ],
    'modules' => [
        'customer' => [
            'class' => api\modules\customer\Module::class,
            'modules' => [
                'v1' => [
                    'class' => api\modules\customer\v1\Module::class,
                ]
            ]
        ],
        'employee' => [
            'class' => api\modules\employee\Module::class,
            'modules' => [
                'v1' => [
                    'class' => api\modules\employee\v1\Module::class,
                ]
            ]
        ],
    ],
    'components' => [
        'request' => [
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'user' => [
            'identityClass' => \common\ar\User::class,
            'enableAutoLogin' => false,
            'identityCookie' => false,
            'enableSession' => false,
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
            'class' => ApiErrorHandler::class,
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        '/' => 'customer/v1',
                    ],
                    'pluralize' => false,
                    'prefix' => 'customer/v1',
                    'extraPatterns' => [
                        'POST login' => 'auth/login',

                        'GET profile' => 'profile/view',
                        'PUT profile' => 'profile/update',
                        'POST profile/photo' => 'profile/upload-image',
                        'GET profile/transactions' => 'profile/transactions',
                        'POST profile/fcm-token' => 'profile/add-fcm-token',
                        'DELETE profile/fcm-token/<token>' => 'profile/remove-fcm-token',
                        'DELETE profile/remove-all-fcm-tokens' => 'profile/remove-all-fcm-tokens',

                        'GET bid' => 'bid/index',
                        'GET bid/<bidId:[\d\-]+>' => 'bid/view',
                        'POST bid' => 'bid/create',
                        'GET bid/search' => 'bid/search',
                        'DELETE bid/<bidId:[\d\-]+>' => 'bid/cancel',
                        'PUT bid/approve/<bidId:[\d\-]+>' => 'bid/approve',

                        'GET work' => 'work/index',
                        'GET category' => 'category/index',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        '/' => 'employee/v1',
                    ],
                    'pluralize' => false,
                    'prefix' => 'employee/v1',
                    'extraPatterns' => [
                        'POST login' => 'auth/login',

                        'GET profile' => 'profile/view',
                        'PUT profile' => 'profile/update',
                        'POST profile/photo' => 'profile/upload-image',
                        'POST profile/fcm-token' => 'profile/add-fcm-token',
                        'DELETE profile/fcm-token/<token>' => 'profile/remove-fcm-token',
                        'DELETE profile/remove-all-fcm-tokens' => 'profile/remove-all-fcm-tokens',

                        'GET bid' => 'bid/index',
                        'GET bid/<bidId:[\d\-]+>' => 'bid/view',
                        'GET bid/search' => 'bid/search',
                        'GET bid/search-all-open' => 'bid/search-all-open',
                        'PUT bid/apply/<bidId:[\d\-]+>' => 'bid/apply',
                        'PUT bid/done/<bidId:[\d\-]+>' => 'bid/done',
                        'GET category' => 'category/index',
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];
