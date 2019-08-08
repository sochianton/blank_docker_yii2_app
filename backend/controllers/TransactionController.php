<?php

namespace backend\controllers;

use backend\models\search\TransactionSearch;
use common\service\TransactionService;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Class TransactionController
 * @package backend\controllers
 */
class TransactionController extends Controller
{
    /**
     * @var TransactionService
     */
    private $transactionService;

    /**
     * BidController constructor.
     * @param $id
     * @param $module
     * @param TransactionService $transactionService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        TransactionService $transactionService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->transactionService = $transactionService;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'index',
                        ],
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Bid models.
     * @return mixed
     * @throws \Exception
     */
    public function actionIndex()
    {
        $searchModel = new TransactionSearch();
        $request = Yii::$app->request;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search($request->queryParams, $request->isPost),
        ]);
    }
}
