<?php

namespace console\controllers;

use scl\authLib\AuthModule;
use yii\console\Controller;

/**
 * Class AuthRepoInit
 *
 * @package console\controllers
 */
class AuthRepoController extends Controller
{
    public function actionInit()
    {
        /**@var AuthModule $auth */
        $auth = \Yii::$container->get(AuthModule::class);
        $auth->initRepository();
    }
}