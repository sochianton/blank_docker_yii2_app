<?php

namespace common\controllers;


use common\filters\AdminRBACFilter;
use yii\web\Controller;


class AppController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AdminRBACFilter::class,
            ]
        ];
    }

}