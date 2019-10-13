<?php

namespace scl\yii\tools\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\Response;


/**
 * App controller
 */
class RestController extends Controller
{
    /** @var array $input */
    public $input;

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => [],
        ];
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        $behaviors['contentNegotiator']['languages'] = ['ru', 'en'];
        $behaviors['access']['class'] = AccessControl::class;
        $behaviors['access']['rules'][] = [
            'actions' => [],
            'allow' => true,
            'roles' => ['@'],
        ];
        // Отключён фильтр RateLimiter
        unset($behaviors['rateLimiter']);
        return $behaviors;
    }

    public function init_new()
    {

        $this->input = Yii::$app->request->bodyParams;

        Yii::info(print_r($this->input, true));

//        $get = Yii::$app->request->get();
//        if(Yii::$app->request->isGet){
//            $this->input = $get;
//        }
//        else{
//            $params = Yii::$app->request->bodyParams;
//            if(is_string($params)) $params = Json::decode($params);
//            $this->input = ArrayHelper::merge($get, $params);
//
//        }
        parent::init();
    }

    public function init()
    {

        //Yii::warning('Информация : '.print_r(Yii::$app->request->bodyParams, true));

        $this->input = Yii::$app->request->isGet
            ? Yii::$app->request->get()
            : array_merge(Yii::$app->request->get(), Yii::$app->request->bodyParams);
        parent::init();
    }
}
