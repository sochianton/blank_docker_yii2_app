Graylog2 log target for Yii2
============================

REQUIREMENTS
------------

You should generally follow [Yii 2 requirements](https://github.com/yiisoft/yii2/blob/master/README.md).
The minimum is that your Web server supports PHP 7.0.


Installation
------------

### Install via subtree and composer

Include like subtree to scl/tools directory of project
git subtree add --squash --prefix=scl/composer/yii2-graylog2 git@gitlab.icerockdev.com:scl/scl-yii/graylog.git tag_name

Example `git subtree add --squash --prefix=scl/composer/yii2-graylog2 git@gitlab.icerockdev.com:scl/scl-yii/graylog.git master`

````php
{
    "require": {
        "scl/yii2-graylog2": "@dev"
    },
    "repositories": [
        {
            "type": "path",
            "url":  "scl/composer/yii2-graylog2"
        }
    ]
}
````

Usage
-----

Add Graylog target to your log component config:
```php
<?php
return [
    //...
    'components' => [
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
                    'categories' => ['application'],
                    'shortLength' => 100, // Limit of short message size, Default 100
                    'logVars' => ['_GET', '_POST', '_FILES'], // Default list for logging like context
                    'contextLength' => 500, // Max length of context string, Default 500
                    'contextDepth' => 5, // Max depth of dump logVars, Default 5
                    'throwable' => [ // Fields, sended on exception
                        'field' => 'val in exception',
                    ], 
                    'logApiHeaders' => false, // Flag for send special request header every request, Default false
                    'headerMap' => [ // list of headers for logging. Map graylogField => header. This is default map 
                         'device-model' => 'X-Device-Model',
                         'platform' => 'X-Platform',
                         'platform-version' => 'X-Platform-Version',
                         'app-version' => 'X-App-Version',
                         'app-build' => 'X-App-Build',
                    ],
                    'host' => '127.0.0.1',
                    'facility' => 'facility-name',
                    'additionalFields' => [
                        'tag' => 'tag-name'
                    ]
                ],
            ],
        ],
    ],
    //...
];
```

GraylogTarget will use traces array (first element) from log message to set `file` and `line` gelf fields. So if you want to see these fields in Graylog2, you need to set `traceLevel` attribute of `log` component to 1 or more. Also all lines from traces will be sent as `trace` additional gelf field.

You can log not only strings, but also any other types (non-strings will be dumped by `yii\helpers\VarDumper::dumpAsString()`).

By default GraylogTarget will put the entire log message as `short_message` gelf field. But you can set `short_message`, `full_message` and `additionals` by using `'short'`, `'full'` and `'add'` keys respectively:
```php
<?php
// short_message will contain string representation of ['test1' => 123, 'test2' => 456],
// no full_message will be sent
Yii::info([
    'test1' => 123,
    'test2' => 456,
]);

// short_message will contain 'Test short message',
// two additional fields will be sent,
// full_message will contain all other stuff without 'short' and 'add':
// string representation of ['test1' => 123, 'test2' => 456]
Yii::info([
    'test1' => 123,
    'test2' => 456,
    'short' => 'Test short message',
    'add' => [
        'additional1' => 'abc',
        'additional2' => 'def',
    ],
]);

// short_message will contain 'Test short message',
// two additional fields will be sent,
// full_message will contain 'Test full message', all other stuff will be lost
Yii::info([
    'test1' => 123,
    'test2' => 456,
    'short' => 'Test short message',
    'full' => 'Test full message',
    'add' => [
        'additional1' => 'abc',
        'additional2' => 'def',
    ],
]);
```
