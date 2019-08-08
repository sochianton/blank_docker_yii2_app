# yii2-push
### Send push extension for the Yii2 framework


## REQUIREMENTS

You should generally follow [Yii 2 requirements](https://github.com/yiisoft/yii2/blob/master/README.md).
The minimum is that your Web server supports PHP 7.1.


## INSTALLATION

### Install via Composer

За ключиком обращаться к админу
````php
{
    "require": {
        "scl/yii2-push": "@dev"
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "https://<user>:<key>@gitlab.icerockdev.com/scl/scl-yii/yii2-push.git"
        }
    ]
}
````

## CONFIGURING

#### Define repo with push data access
````php
class PushRepo implements \scl\yii\push\PushRepoInterface
{

    /**
     * Get token list by user id list
     * Do not return empty string
     * @param int[] $uidList
     * @return string[]
     */
    public function getPushTokenListById(array $uidList): array
    {
        return [];
    }

    /**
     * Remove tokens from storage by token list
     * @param string[] $tokenList
     * @return bool true for successful, false - otherwise
     */
    public function removeTokensById(array $tokenList): bool
    {
        return [];
    }
}

````

#### Set component init to bootstrap
````php
class SetContainer implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param \yii\base\Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $container = Yii::$container;

        $pushRepo = new PushRepo();
        $fcmKey = 'some key';

        $container->setSingleton(Push::class, new Push($pushRepo, $fcmKey));
    }
}

````

#### Include in config bootstrap
````php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap' => [
        'common\bootstrap\SetContainer',
    ],
    ...
];
````

## USAGE

````php
function test()
{
    $notification = new Notification('title', 'body');
    $notification->setBadge(5);
    $notification->setClickAction('some_click_action');

    // direct send
    $container = Yii::$container;

    /** @var Push $pushService */
    $pushService = $container->get(Push::class);
    $pushService->sendToUsers([1,3,5], $notification);

    // queue send by uid list
    \Yii::$app->queue->push(new PushUserJob([1,2], $notification));
    // queue send by token list
    \Yii::$app->queue->push(new PushTokenJob(['djkfgnjkdfg','jhbdfhgrt'], $notification));
}

```` 


