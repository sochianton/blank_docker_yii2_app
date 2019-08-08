<?php

namespace common\bootstrap;

use common\repository\PushRepository;
use scl\yii\push\Push;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;

/**
 * Class SetContainer
 */
class SetContainer implements BootstrapInterface
{

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $container = Yii::$container;

        $pushRepo = new PushRepository();
        $fcmKey = 'AAAASMuKkQE:APA91bGmTn5HOUQrBvp3sOpz1X_iNp3oGfm7nxy0ngcityYpfuRDrZUBYOQpyQmLt3bcEOPpQqWp7nL_5HO6-xoLuSsOoybdhChKzyNgjfOwHj1P6YP5XRzxCbv9BbWHa2B8YtdGeFdX';

        $container->setSingleton(Push::class, new Push($pushRepo, $fcmKey));
    }
}
