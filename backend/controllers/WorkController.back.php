<?php

namespace backend\controllers;

use backend\models\forms\WorkCreateForm;
use backend\models\forms\WorkUpdateForm;
use backend\models\search\WorkSearch;
use common\models\Work;
use common\service\QualificationService;
use common\service\WorkService;
use Yii;
use yii\base\Module;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * @deprecated
 * Class __WorkController
 * @package backend\controllers
 */
class __WorkController extends Controller
{
    /** @var QualificationService $qualificationService */
    protected $qualificationService;
    /** @var WorkService $workService */
    protected $workService;

    /**
     * WorkController constructor.
     * @param string $id
     * @param Module $module
     * @param WorkService $workService
     * @param QualificationService $qualificationService
     * @param array $config
     */
    public function __construct(
        string $id,
        Module $module,
        WorkService $workService,
        QualificationService $qualificationService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->workService = $workService;
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
                            'update',
                            'create',
                            'view',
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
                    'block' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionIndex()
    {
        $searchModel = new WorkSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'workService' => $this->workService,
            'qualificationService' => $this->qualificationService,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $work = $this->workService->get($id);

        return $this->render('view', [
            'model' => $work,
            'workService' => $this->workService,
            'qualificationService' => $this->qualificationService,
        ]);
    }

    /**
     * @return string|Response
     * @throws \Throwable
     */
    public function actionCreate()
    {
        $request = new WorkCreateForm();
        $qualifications = $this->qualificationService->getList();

        if ($request->load(Yii::$app->request->post()) && $request->validate()) {
            try {
                /** @var Work $newWork */
                $newWork = $this->workService->create($request);
                Yii::$app->session->setFlash('success', Yii::t('app', 'Work is created.'));

                return $this->redirect(['work/view', 'id' => $newWork->id]);
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', Yii::t('errors', $e->getMessage()));
            }
        }

        return $this->render('create', [
            'model' => $request,
            'qualifications' => $qualifications,
        ]);
    }

    /**
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionUpdate(int $id)
    {
        $work = $this->workService->get($id);
        $qualifications = $this->qualificationService->getList();
        $selectedQualifications = $this->workService->getQualificationIds($id);

        $request = new WorkUpdateForm();
        $request->fillFromModel($work, $selectedQualifications);

        if ($request->load(Yii::$app->request->post()) && $request->validate()) {

//            die('<pre>'.print_r($request->errors, true).'</pre>');

            try {
                /** @var Work $newWork */
                $newWork = $this->workService->update($id, $request->getDto());
                Yii::$app->session->setFlash('success', Yii::t('app', 'Work is updated.'));

                return $this->redirect(['work/view', 'id' => $newWork->id]);
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', Yii::t('errors', $e->getMessage()));
            }
        }

        return $this->render('update', [
            'model' => $request,
            'qualifications' => $qualifications,
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionBlock($id)
    {
        if ($this->workService->block($id)) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Work is blocked.'));
        }

        return $this->redirect('/work/index');
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionRestore($id)
    {
        if ($this->workService->restore($id)) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Work is restored.'));
        }

        return $this->redirect('/work/index');
    }
}
