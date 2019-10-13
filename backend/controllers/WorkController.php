<?php

namespace backend\controllers;

use common\ar\Work;
use common\controllers\CRUDController;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class WorkController extends CRUDController
{
    public $model = Work::class;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'update',
                            'create',
                            'delete',
                        ],
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function init()
    {
        $this->indexTitle = Yii::t('app', 'Works');

        parent::init();
    }
}
