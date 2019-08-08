<?php

use paragraph1\phpFCM\Notification;
use scl\yii\push\Push;
use yii\base\BootstrapInterface;

///// Define repo

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

///// DI settings
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

///// Example call

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
