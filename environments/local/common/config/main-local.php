<?php

return [
    'bootstrap' => [
        'gii',
    ],
    'modules' => [
        'gii' => [
            'class' => yii\gii\Module::class,
            'allowedIPs' => ['*']
        ],
    ],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=' . $_ENV['POSTGRES_HOST'] . ';port=5432;dbname=' . $_ENV['POSTGRES_DB'],
            'username' => $_ENV['POSTGRES_USER'],
            'password' => $_ENV['POSTGRES_PASSWORD'],
            'charset' => 'utf8',
        ],
        'Yii2Twilio' => [
            'class' => filipajdacic\yiitwilio\YiiTwilio::class,
            'account_sid' => 'YOUR_TWILIO_ACCOUNT_SID_HERE',
            'auth_key' => 'YOUR_TWILIO_AUTH_KEY_HERE',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => $_ENV['EMAIL_SERVER'],
                'username' => $_ENV['EMAIL_USER'] ?? null,
                'password' => $_ENV['EMAIL_PASSWORD'] ?? null,
                'port' => $_ENV['EMAIL_PORT'],
                'encryption' => $_ENV['EMAIL_ENCRYPTION'], // It is often used, check your provider or mail server specs
            ],
        ],
    ],
];
