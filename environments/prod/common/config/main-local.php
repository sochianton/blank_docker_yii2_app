<?php

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=' . $_ENV['POSTGRES_HOST'] . ';port=5432;dbname=' . $_ENV['POSTGRES_DB'],
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'Yii2Twilio' => [
            'class' => filipajdacic\yiitwilio\YiiTwilio::class,
            'account_sid' => 'YOUR_TWILIO_ACCOUNT_SID_HERE',
            'auth_key' => 'YOUR_TWILIO_AUTH_KEY_HERE',
        ],
        'mailer' => [
            'class' => scl\mailer\Mailer::class,
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
            'useQueue' => true, // flag for async send
            'queue' => 'queue', // queue component, need for async send
            'logCategory' => 'mailer',
            'messageConfig' => [
                'from' => [$_ENV['EMAIL_USER'] ?? null => 'Euroservice']
            ],
            'config' => [
                'mailer' => 'smtp',
                'host' => $_ENV['EMAIL_SERVER'],
                'port' => $_ENV['EMAIL_PORT'],
                'smtpsecure' => $_ENV['EMAIL_ENCRYPTION'],
                'smtpauth' => true,
                'username' => $_ENV['EMAIL_USER'] ?? null,
                'password' => $_ENV['EMAIL_PASSWORD'] ?? null,
            ],
        ],
    ],
];
