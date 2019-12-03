<?php

namespace backend\controllers;

use common\ar\Transactions;
use common\controllers\CRUDController;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * Class TransactionController
 * @package backend\controllers
 */
class TransactionController extends CRUDController
{
    public $model = Transactions::class;


    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
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
        $this->indexTitle = Yii::t('app', 'Transactions');
        $this->createTitle = Yii::t('app', 'Add manual transaction');

        parent::init();
    }

}
