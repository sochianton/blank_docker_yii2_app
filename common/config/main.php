<?php

use yii\queue\amqp_interop\Queue;
use yii\queue\LogBehavior;

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'name' => 'euroservice',
    'language' => 'ru',
    'sourceLanguage' => 'en',
    'bootstrap' => [
        'log',
        'queue',
        common\bootstrap\SetContainer::class,
        common\events\Events::class,
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'assetManager' => [
            'hashCallback' => function ($path) {
                $path = str_replace(Yii::getAlias('@root'), '', $path);
                return substr(hash('md4', $path), 0, 8);
            }
        ],
        'queue' => [
            'class' => Queue::class,
            'host' => $_ENV['RABBITMQ_HOST'],
            'port' => $_ENV['RABBITMQ_PORT'],
            'user' => $_ENV['RABBITMQ_DEFAULT_USER'],
            'password' => $_ENV['RABBITMQ_DEFAULT_PASS'],
            'queueName' => 'queue',
            'driver' => Queue::ENQUEUE_AMQP_LIB,
            'as log' => LogBehavior::class,
        ],
        'notifierSMS' => [
            'class' => common\components\TwilioSMSNotifier::class,
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
