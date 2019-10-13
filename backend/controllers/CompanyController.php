<?php


namespace backend\controllers;


use common\ar\Company;
use common\controllers\CRUDController;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\services\CompanyService;

class CompanyController extends CRUDController
{

    public $model = Company::class;

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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function init()
    {
        $this->indexTitle = Yii::t('app', 'Companies');

        parent::init();
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
        if(CompanyService::block($id)){
            Yii::$app->session->setFlash('success', Yii::t('app', 'Company is blocked.'));
        }
        else{
            Yii::$app->session->setFlash('error', Yii::t('app', 'Can\'t perform operation'));
        }

        return $this->redirect(['index']);
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
        if(CompanyService::restore($id)){
            Yii::$app->session->setFlash('success', Yii::t('app', 'Company is restored.'));
        }
        else{
            Yii::$app->session->setFlash('error', Yii::t('app', 'Can\'t perform operation'));
        }

        return $this->redirect(['index']);
    }

}