<?php

namespace api\modules\employee;

use common\ar\User;
use Yii;

/**
 * Class Module
 *
 * @package api\modules\employee
 */
class Module extends \yii\base\Module
{
    /**
     * @var string
     */
    public $controllerNamespace = 'api\modules\employee\controllers';

    public function init()
    {
        parent::init();
        Yii::$app->setComponents([
            'user' => [
                'class' => yii\web\User::class,
                'identityClass' => User::class,
                'enableAutoLogin' => false,
            ]
        ]);
        Yii::$app->user->enableSession = false;
    }
}
