<?php

namespace backend\controllers;

use backend\models\forms\CompanyForm;
use backend\models\search\CompanySearch;
use common\models\Company;
use common\service\CompanyService;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * CompanyController implements the CRUD actions for Company model.
 */
class back extends Controller
{
    /**
     * @var CompanyService
     */
    private $companyService;

    /**
     * CompanyController constructor.
     * @param $id
     * @param $module
     * @param CompanyService $companyService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        CompanyService $companyService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
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
     * Lists all Company models.
     * @return mixed
     * @throws \Exception
     */
    public function actionIndex()
    {
        $searchModel = new CompanySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Company model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id)
    {
        $company = $this->companyService->get($id);

        return $this->render('view', [
            'model' => $company,
        ]);
    }

    /**
     * Creates a new Company model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \Throwable
     * @throws ServerErrorHttpException
     */
    public function actionCreate()
    {
        /** @var CompanyForm $request */
        $request = new CompanyForm();

        if ($request->load(Yii::$app->request->bodyParams) && $request->validate()) {
            /** @var Company $company */
            $company = $this->companyService->create($request->getDto());
            return $this->redirect(['view', 'id' => $company->id]);
        }

        return $this->render('create', [
            'model' => $request,
        ]);
    }

    /**
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionUpdate(int $id)
    {
        $request = new CompanyForm();

        if ($request->load(Yii::$app->request->bodyParams)) {
            if ($request->validate()) {
                $this->companyService->update($id, $request->getDto());
                return $this->redirect(['view', 'id' => $id]);
            }
        } else {
            $company = $this->companyService->get($id);
            $request->fillFromModel($company);
        }

        return $this->render('update', [
            'model' => $request,
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionBlock($id)
    {
        if ($this->companyService->block($id)) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Company is blocked.'));
        }

        return $this->redirect('/company/index');
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionRestore($id)
    {
        if ($this->companyService->restore($id)) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Company is restored.'));
        }

        return $this->redirect('/company/index');
    }
}
