<?php
return yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/main.php',
    require __DIR__ . '/main-local.php',
    require __DIR__ . '/test.php',
    [
        'components' => [
            'db' => [
                'dsn' => 'pgsql:host=' . $_ENV['POSTGRES_HOST'] . ';port=5432;dbname=' . $_ENV['POSTGRES_DB'] . '_test',
            ]
        ],
    ]
);
