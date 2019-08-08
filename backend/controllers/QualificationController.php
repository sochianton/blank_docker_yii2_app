<?php

namespace backend\controllers;

use backend\models\forms\QualificationCreateForm;
use backend\models\forms\QualificationUpdateForm;
use backend\models\search\QualificationSearch;
use common\models\Qualification;
use common\service\QualificationService;
use Yii;
use yii\base\Module;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class QualificationController
 * @package backend\controllers
 */
class QualificationController extends Controller
{
    /** @var QualificationService $qualificationService */
    protected $qualificationService;

    /**
     * QualificationController constructor.
     * @param string $id
     * @param Module $module
     * @param QualificationService $qualificationService
     * @param array $config
     */
    public function __construct(
        string $id,
        Module $module,
        QualificationService $qualificationService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
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
        $searchModel = new QualificationSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $qualification = $this->qualificationService->get($id);

        return $this->render('view', [
            'model' => $qualification,
        ]);
    }

    /**
     * @return string|Response
     * @throws \Throwable
     */
    public function actionCreate()
    {
        $request = new QualificationCreateForm;
        if ($request->load(\Yii::$app->request->post()) && $request->validate()) {
            try {
                /** @var Qualification $newQualification */
                $newQualification = $this->qualificationService->create($request);
                Yii::$app->session->setFlash('success', Yii::t('app', 'Qualification is created.'));

                return $this->redirect(['qualification/view', 'id' => $newQualification->id]);
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', Yii::t('errors', $e->getMessage()));
            }
        }

        return $this->render('create', [
            'model' => $request,
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionBlock($id)
    {
        if ($this->qualificationService->block($id)) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Qualification is blocked.'));
        }

        return $this->redirect('/qualification/index');
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionRestore($id)
    {
        if ($this->qualificationService->restore($id)) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Qualification is restored.'));
        }

        return $this->redirect('/qualification/index');
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $qualification = $this->qualificationService->get($id);

        $request = new QualificationUpdateForm();
        $request->fillFromModel($qualification);

        if ($request->load(\Yii::$app->request->post()) && $request->validate()) {
            try {
                /** @var Qualification $newQualification */
                $newQualification = $this->qualificationService->update($request->getDto());
                Yii::$app->session->setFlash('success', Yii::t('app', 'Qualification is updated.'));

                return $this->redirect(['qualification/view', 'id' => $newQualification->id]);
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', Yii::t('errors', $e->getMessage()));
            }
        }

        return $this->render('update', [
            'model' => $request,
        ]);
    }

}
