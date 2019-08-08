<?php

namespace api\modules\customer;

use common\models\Customer;
use Yii;

/**
 * Class Module
 *
 * @package api\modules\customer
 */
class Module extends \yii\base\Module
{
    /**
     * @var string
     */
    public $controllerNamespace = 'api\modules\customer\controllers';

    public function init()
    {
        parent::init();
        Yii::$app->setComponents([
            'user' => [
                'class' => yii\web\User::class,
                'identityClass' => Customer::class,
                'enableAutoLogin' => false,
            ]
        ]);
        Yii::$app->user->enableSession = false;
    }
}
