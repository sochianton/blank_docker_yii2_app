<?php

namespace backend\controllers;

use common\ar\Qualification;
use common\controllers\CRUDController;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * Class QualificationController
 * @package backend\controllers
 */
class QualificationController extends CRUDController
{

    public $model = Qualification::class;


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
        $this->indexTitle = Yii::t('app', 'Categories');

        parent::init();
    }

}
