# yii2-phpmailer
### Phpmailer extension for the Yii framework


This extension adds integration of popular **[PHPMailer](https://github.com/PHPMailer/PHPMailer)** 

Although extension classes implement `yii\mail\MailerInterface` and `yii\mail\MessageInterface`, some methods of Yii 2
`BaseMailer` and `BaseMessage` are overriden - mainly because of PHPMailer-specific issues.

Best feature - send mail by queue

## REQUIREMENTS

You should generally follow [Yii 2 requirements](https://github.com/yiisoft/yii2/blob/master/README.md).
The minimum is that your Web server supports PHP 7.0.


## INSTALLATION

### Install via subtree and composer

Include like subtree to scl/tools directory of project
git subtree add --squash --prefix=scl/composer/yii2-mailer git@gitlab.icerockdev.com:scl/scl-yii/mailer.git tag_name

Example `git subtree add --squash --prefix=scl/composer/yii2-mailer git@gitlab.icerockdev.com:scl/scl-yii/mailer.git master`

=======
````json
{
    "require": {
        "scl/mailer": "@dev"
    },
    "repositories": [
        {
            "type": "path",
            "url":  "scl/composer/yii2-mailer"
        }
    ]
}
````

## CONFIGURING

To use this extension, you should add some settings in your application configuration file.
It may be like the following:

```php
return [
//....
	'components' => [
        'mailer' => [
            'class' => scl\mailer\Mailer::class,
            'viewPath'         => '@common/mail',
            'useFileTransport' => false,
            'useQueue' => true, // flag for async send
            'queue' => 'queue', // queue component, need for async send
            'logCategory' => 'mailer',
            'config'           => [
                'mailer'     => 'smtp',
                'host'       => 'smtp.yandex.ru',
                'port'       => '465',
                'smtpsecure' => 'ssl',
                'smtpauth'   => true,
                'username'   => 'username',
                'password'   => 'password',
            ],
        ],
	],
];
```

## USAGE

Example of simple usage:

```php
Yii::$app->mailer->compose()
     ->setFrom(['noreply@example.com' => 'My Example Site'])
     ->setTo([$form->email => $form->name])
     ->setSubject($form->subject)
     ->setTextBody($form->text)
     ->send();
```
