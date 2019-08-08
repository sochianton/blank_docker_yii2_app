<?php

namespace api\controllers;

use stdClass;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @return stdClass
     */
    public function actionIndex(): stdClass
    {
        return new stdClass();
    }
}
