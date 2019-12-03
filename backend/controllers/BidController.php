<?php

namespace backend\controllers;


use common\ar\Bid;
use common\controllers\CRUDController;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * Class BidController
 * @package backend\controllers
 */
class BidController extends CRUDController
{

    public $model = Bid::class;

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
//            'access' => [
//                'class' => AccessControl::class,
//                'rules' => [
//                    [
//                        'actions' => [
//                            'index',
//                            'update',
//                            'create',
//                            'delete',
//                        ],
//                        'allow' => true,
//                        'roles' => ['@']
//                    ],
//                ],
//            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }

    public function init()
    {
        $this->indexTitle = Yii::t('app', 'Bids');

        parent::init();
    }

}
