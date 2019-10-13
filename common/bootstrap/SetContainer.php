<?php

namespace common\bootstrap;

use common\repositories\PushRep;
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

        $pushRepo = new PushRep();
//        $fcmKey = 'AAAASMuKkQE:APA91bGmTn5HOUQrBvp3sOpz1X_iNp3oGfm7nxy0ngcityYpfuRDrZUBYOQpyQmLt3bcEOPpQqWp7nL_5HO6-xoLuSsOoybdhChKzyNgjfOwHj1P6YP5XRzxCbv9BbWHa2B8YtdGeFdX';
        $fcmKey = 'AAAADqwmRCQ:APA91bF854yWYj622WrjOMClE48bqbto4mcESzNXOA44k32H-D0gJQ6iaKZPiP4ikmwqonubWDYvTmG73b91A3LreowcujyuqDZl2eFyEgqDJDjXukNW_FbCoT40Wn5BD-MGxSUptDS7';

        $container->setSingleton(Push::class, new Push($pushRepo, $fcmKey));
    }
}
