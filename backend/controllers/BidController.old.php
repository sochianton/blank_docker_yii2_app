<?php

namespace backend\controllers;

use backend\models\forms\BidCreateForm;
use backend\models\forms\BidUpdateForm;
use backend\models\search\BidSearch;
use common\models\Bid;
use common\models\BidAttachment;
use common\service\BidService;
use common\service\CustomerService;
use common\service\EmployeeService;
use common\service\WorkService;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class BidController
 * @package backend\controllers
 */
class BidController_ extends Controller
{
    /**
     * @var BidService
     */
    private $bidService;
    /**
     * @var CustomerService
     */
    private $customerService;
    /**
     * @var EmployeeService
     */
    private $employeeService;
    /**
     * @var WorkService
     */
    private $workService;

    /**
     * BidController constructor.
     * @param $id
     * @param $module
     * @param BidService $bidService
     * @param CustomerService $customerService
     * @param EmployeeService $employeeService
     * @param WorkService $workService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        BidService $bidService,
        CustomerService $customerService,
        EmployeeService $employeeService,
        WorkService $workService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->bidService = $bidService;
        $this->customerService = $customerService;
        $this->employeeService = $employeeService;
        $this->workService = $workService;
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
                            //'block',
                            //'restore',
                            'ajax-delete-file'
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
     * Lists all Bid models.
     * @return mixed
     * @throws \Exception
     */
    public function actionIndex()
    {
        $searchModel = new BidSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Bid model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $bid = $this->bidService->get($id);
        $customerPhotos = $this->bidService->getFiles($id, BidAttachment::TYPE_PHOTO_CUSTOMER);
        $employeePhotos = $this->bidService->getFiles($id, BidAttachment::TYPE_PHOTO_EMPLOYEE);

        return $this->render('view', [
            'model' => $bid,
            'customerPhotos' => $customerPhotos,
            'employeePhotos' => $employeePhotos,
        ]);
    }

    /**
     * Creates a new Bid model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws Throwable
     */
    public function actionCreate()
    {
        $request = new BidCreateForm();
        $customers = $this->customerService->getList();
        $employees = $this->employeeService->getList();
        $works = $this->workService->getList();

        if ($request->load(Yii::$app->request->post()) && $request->validate()) {

            //die('<pre>'.print_r($request->getDto(), true).'</pre>');
            //die('<pre>'.print_r($request->getDto(), true).'</pre>');

            try {
                /** @var Bid $bid */
                $bid = $this->bidService->create($request->getDto());
                Yii::$app->session->setFlash('success', Yii::t('app', 'Bid is created.'));
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', Yii::t('errors', $e->getMessage()));
            }
        }

        return $this->render('create', [
            'model' => $request,
            'customers' => $customers,
            'employees' => $employees,
            'works' => $works,
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    public function actionUpdate($id)
    {
        $bid = $this->bidService->get($id);
        $customers = $this->customerService->getList();
        $employees = $this->employeeService->getList();
        $works = $this->workService->getList();

        $customerPhotos = $this->bidService->getFiles($id, BidAttachment::TYPE_PHOTO_CUSTOMER);
        $employeePhotos = $this->bidService->getFiles($id, BidAttachment::TYPE_PHOTO_EMPLOYEE);
        $files = $this->bidService->getFiles($id, BidAttachment::TYPE_FILE);
        $selectedWorks = $this->bidService->getWorkIds($id);

        $request = new BidUpdateForm();
        $request->fillFromModel($bid, $selectedWorks);

        if ($request->load(Yii::$app->request->post()) && $request->validate()) {
            try {
                /** @var Bid $bid */
                $bid = $this->bidService->update($id, $request->getDto());
                Yii::$app->session->setFlash('success', Yii::t('app', 'Bid is updated.'));

                return $this->redirect(['bid/view', 'id' => $bid->id]);
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', Yii::t('errors', $e->getMessage()));
            }
        }

        return $this->render('update', [
            'model' => $request,
            'customers' => $customers,
            'employees' => $employees,
            'works' => $works,
            'customerPhotos' => $customerPhotos,
            'employeePhotos' => $employeePhotos,
            'files' => $files,
        ]);
    }

    /**
     * @return \stdClass
     * @throws NotFoundHttpException
     */
    public function actionAjaxDeleteFile()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->bidService->deleteFile(Yii::$app->request->post('key'));

        return new \stdClass();
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    /*public function actionBlock($id)
    {
        if ($this->bidService->block($id)) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Bid is blocked.'));
        }

        return $this->redirect('/bid/index');
    }*/

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    /*public function actionRestore($id)
    {
        if ($this->bidService->restore($id)) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Bid is restored.'));
        }

        return $this->redirect('/bid/index');
    }*/
}
