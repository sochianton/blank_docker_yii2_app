<?php

namespace backend\controllers;

use backend\models\forms\EmployeeForm;
use backend\models\search\EmployeeSearch;
use common\models\Company;
use common\models\Employee;
use common\service\CompanyService;
use common\service\EmployeeService;
use common\service\QualificationService;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * EmployeeController implements the CRUD actions for Employee model.
 */
class EmployeeController extends Controller
{
    /**
     * @var EmployeeService
     */
    private $employeeService;
    /**
     * @var CompanyService
     */
    private $companyService;
    /**
     * @var QualificationService
     */
    private $qualificationService;

    /**
     * EmployeeController constructor.
     * @param $id
     * @param $module
     * @param EmployeeService $employeeService
     * @param CompanyService $companyService
     * @param QualificationService $qualificationService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        EmployeeService $employeeService,
        CompanyService $companyService,
        QualificationService $qualificationService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->employeeService = $employeeService;
        $this->companyService = $companyService;
        $this->qualificationService = $qualificationService;
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
     * Lists all Employee models.
     * @return mixed
     * @throws \Exception
     */
    public function actionIndex()
    {
        $searchModel = new EmployeeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'employeeService' => $this->employeeService,
            'qualificationService' => $this->qualificationService
        ]);
    }

    /**
     * Displays a single Employee model.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id)
    {
        $employee = $this->employeeService->get($id);

        return $this->render('view', [
            'model' => $employee,
            'employeeService' => $this->employeeService,
            'qualificationService' => $this->qualificationService
        ]);
    }

    /**
     * Creates a new Employee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws Throwable
     */
    public function actionCreate()
    {
        /** @var EmployeeForm $request */
        $request = new EmployeeForm();
        $request->scenario = 'create';
        $qualifications = $this->qualificationService->getList();

        if ($request->load(Yii::$app->request->bodyParams) && $request->validate()) {
            /** @var Employee $employee */
            $employee = $this->employeeService->create($request->getDto());

            if ($employee->validate()) {
                return $this->redirect(['view', 'id' => $employee->id]);
            } else {
                $request->addErrors($employee->getErrors());
            }
        }

        $companies = $this->companyService->getList(Company::TYPE_CONTRACTOR);

        return $this->render('create', [
            'model' => $request,
            'companies' => $companies,
            'qualifications' => $qualifications,
        ]);
    }

    /**
     * Updates an existing Employee model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionUpdate($id)
    {
        $employee = $this->employeeService->get($id);
        $qualifications = $this->qualificationService->getList();
        $selectedQualifications = $this->employeeService->getQualificationIds($id);

        $request = new EmployeeForm();
        $request->fillFromModel($employee, $selectedQualifications);

        if ($request->load(Yii::$app->request->bodyParams) && $request->validate()) {
            try {
                $employee = $this->employeeService->update($id, $request->getDto());
                Yii::$app->session->setFlash('success', Yii::t('app', 'Employee is updated.'));
                if ($employee->validate()) {
                    return $this->redirect(['view', 'id' => $employee->id]);
                } else {
                    $request->addErrors($employee->getErrors());
                }
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', Yii::t('errors', $e->getMessage()));
            }
        }

        $companies = $this->companyService->getList(Company::TYPE_CONTRACTOR);

        return $this->render('update', [
            'model' => $request,
            'companies' => $companies,
            'qualifications' => $qualifications,
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
        if ($this->employeeService->block($id)) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Employee is blocked.'));
        }

        return $this->redirect('/employee/index');
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
        if ($this->employeeService->restore($id)) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Employee is restored.'));
        }

        return $this->redirect('/employee/index');
    }
}
