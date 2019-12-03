<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'name' => 'ИАС',

    'language' => 'ru-RU',
    'sourceLanguage' => 'en-EN',

    'timeZone' => 'Europe/Moscow',


    'bootstrap' => [
        'log',
        'queue',
        common\bootstrap\SetContainer::class,
        common\events\Events::class,
    ],
    'components' => [
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@app/themes/basic',
                ],
            ],
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'assetManager' => [
            'linkAssets' => true,
//            'fileMode' => '0755',
//            'forceCopy' => true,
            'appendTimestamp' => true,
//            'hashCallback' => function ($path) {
//                $path = str_replace(Yii::getAlias('@root'), '', $path);
//                return substr(hash('md4', $path), 0, 8);
//            }
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [

            ],
        ],
        'authManager' => [
            'class' => \yii\rbac\DbManager::class,
            'defaultRoles' => ['user'],
        ],
        'queue' => [
            'class' => yii\queue\amqp_interop\Queue::class,
            'host' => $_ENV['RABBITMQ_HOST'],
            'port' => $_ENV['RABBITMQ_PORT'],
            'user' => $_ENV['RABBITMQ_DEFAULT_USER'],
            'password' => $_ENV['RABBITMQ_DEFAULT_PASS'],
            'queueName' => 'queue',
            'driver' => yii\queue\amqp_interop\Queue::ENQUEUE_AMQP_LIB,
            'as log' => yii\queue\LogBehavior::class,
        ],
        'notifierSMS' => [
            'class' => common\components\TwilioSMSNotifier::class,
        ],
        'formatter' => [
            'defaultTimeZone'=> 'Europe/Moscow',
            'dateFormat'=> 'short',
        ],
        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                ],
                'errors' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                ],
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                'graylog' => [
                    'class' => 'scl\graylog\GraylogTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'host' => '173.212.230.84', // graylog.icerockdev.com - optimize dns resolve
                    'port' => 12201,
                    'facility' => 'euroservice',
                    'additionalFields' => [
                        'env' => YII_ENV,
                        'project-part' => function ($yii) {
                            /** @var \yii\web\Application $yii */
                            return $yii->id ?? 'common';
                        }
                    ]
                ],
            ],
        ],
    ],
];
