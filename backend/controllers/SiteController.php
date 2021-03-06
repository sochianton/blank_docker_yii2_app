<?php

namespace backend\controllers;


use backend\models\forms\LoginForm;
use backend\models\forms\PasswordResetRequestForm;
use backend\services\AuthService;
use common\ar\Push;
use common\ar\User;
use paragraph1\phpFCM\Notification;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ErrorAction;

/**
 * Site controller
 */
class SiteController extends Controller
{

    public $layout = '@app/views/layoutsAuth/main';

    /**
     * @var AuthService
     */
    private $authService;

    /**
     * SiteController constructor.
     * @param $id
     * @param $module
     * @param array $config
     * @param AuthService $authService
     */
    public function __construct($id, $module, AuthService $authService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->authService = $authService;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'except' => [
                    'test',
                ],
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'request-password-reset', 'reset-password'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }




    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
                'layout' => '@app/views/layoutsError/main',
                'view' => '@app/views/layoutsError/error',
                'defaultMessage' => Yii::t('app', 'Page not found'),
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('site/login');
        }
        return $this->redirect('company/index');
    }

    /**
     * Login action.
     * @return string
     */
    public function actionLogin()
    {

        $this->view->title = Yii::t('app', 'Login');

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Requests password reset.
     * @return mixed
     * @throws Exception
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $this->authService->sendNewPassword($model);
            Yii::$app->session->setFlash('success',
                Yii::t('app', 'A new password has been sent to the specified email address.'));
            return $this->goHome();
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * @return string
     * @throws ErrorException
     */
    public function actionTest(){


        $container = Yii::$container;

        /** @var \scl\yii\push\Push $pushService */
        try {
            $pushService = $container->get(\scl\yii\push\Push::class);
        } catch (NotInstantiableException | InvalidConfigException $e) {
            Yii::error('Invalid service configuration: ' . $e->getMessage());
            throw new ErrorException($e->getMessage());
        }


        $allUsers = User::find()->where([
            'status' => User::STATUS_ACTIVE
        ])->column();

        $pushService->sendToUsers($allUsers, Push::TYPE_CUSTOMER, new Notification(
            'TEST',
            'TEST'
        ), ['link'=>'12']);

        return $this->renderContent('TEST');

    }



    // =============================================

    /**
     * Стандартый конфиг для поля в форме авторизации
     * @return array
     */
    static function getFormFieldConfig(){
        return [
            'template' => "{input}\n{hint}\n{error}",
            'errorOptions' => [
                'class' => 'help-block',
                'style' => 'font-size:11px'
            ],
            'inputOptions' => [
                'class' => 'form-control',
                'placeholder' => 'form-control',
            ]
        ];
    }

}
