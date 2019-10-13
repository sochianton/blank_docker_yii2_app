<?php


namespace api\controllers;


use common\models\Work;

use yii\filters\auth\CompositeAuth;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\Response;

class TestController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    //'application/xml' => Response::FORMAT_XML,
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => $this->verbs(),
            ],
            'compositeAuth' => [
                'class' => CompositeAuth::class,
                'authMethods' => [
                    // \yii\filters\auth\HttpBasicAuth::class,
                ],
            ],
//            'rateLimiter' => [
//                'class' => RateLimiter::class
//            ],
        ];
    }

    public function actionIndex(){

        $work = Work::findOne(1);

        return $work;


    }

}