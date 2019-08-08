<?php

namespace backend\controllers;

use backend\models\forms\CustomerForm;
use backend\models\search\CustomerSearch;
use common\models\Company;
use common\models\Customer;
use common\service\CompanyService;
use common\service\CustomerService;
use Yii;
use yii\base\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * CustomerController implements the CRUD actions for Customer model.
 */
class CustomerController extends Controller
{
    /**
     * @var CustomerService
     */
    private $customerService;
    /**
     * @var CompanyService
     */
    private $companyService;

    /**
     * CustomerController constructor.
     * @param $id
     * @param $module
     * @param CustomerService $customerService
     * @param CompanyService $companyService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        CustomerService $customerService,
        CompanyService $companyService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->customerService = $customerService;
        $this->companyService = $companyService;
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
                            'view',
                            'create',
                            'update',
                            'block',
                            'restore',
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

    /**
     * Lists all Customer models.
     * @return mixed
     * @throws \Exception
     */
    public function actionIndex()
    {
        $searchModel = new CustomerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Customer model.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id)
    {
        $customer = $this->customerService->get($id);

        return $this->render('view', [
            'model' => $customer,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     * @throws Exception
     * @throws \Throwable
     */
    public function actionCreate()
    {
        /** @var CustomerForm $request */
        $request = new CustomerForm();
        $request->scenario = 'create';

        if ($request->load(Yii::$app->request->bodyParams) && $request->validate()) {
            /** @var Customer $customer */
            $customer = $this->customerService->create($request->getDto());

            if ($customer->validate()) {
                return $this->redirect(['view', 'id' => $customer->id]);
            } else {
                $request->addErrors($customer->getErrors());
            }

        }

        $companies = $this->companyService->getList(Company::TYPE_CLIENT);

        return $this->render('create', [
            'model' => $request,
            'companies' => $companies,
        ]);
    }

    /**
     * Updates an existing Customer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionUpdate($id)
    {
        $request = new CustomerForm();

        if ($request->load(Yii::$app->request->bodyParams)) {
            if ($request->validate()) {
                $customer = $this->customerService->update($id, $request->getDto());
                if ($customer->validate()) {
                    return $this->redirect(['view', 'id' => $id]);
                } else {
                    $request->addErrors($customer->getErrors());
                }
            }
        } else {
            $customer = $this->customerService->get($id);
            $request->fillFromModel($customer);
        }

        $companies = $this->companyService->getList(Company::TYPE_CLIENT);

        return $this->render('update', [
            'model' => $request,
            'companies' => $companies,
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionBlock($id)
    {
        if ($this->customerService->block($id)) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Customer is blocked.'));
        }

        return $this->redirect('/customer/index');
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionRestore($id)
    {
        if ($this->customerService->restore($id)) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Customer is restored.'));
        }

        return $this->redirect('/customer/index');
    }
}
