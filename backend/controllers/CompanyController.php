<?php


namespace backend\controllers;


use common\ar\Company;
use common\components\CURL;
use common\controllers\CRUDController;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\services\CompanyService;

class CompanyController extends CRUDController
{

    public $model = Company::class;

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
//            'access' => [
//                'class' => AccessControl::class,
//                'rules' => [
//                    [
//                        'actions' => [
//                            'index',
//                            'update',
//                            'create',
//                            'delete',
//                            'block',
//                            'restore',
//
//                            'test',
//                        ],
//                        'allow' => true,
//                        'roles' => ['@']
//                    ],
//                ],
//            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'block' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
        ]);
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


    public function actionTest()
    {

        $res = CURL::authPost('https://sd.e-servis.ru/api/requests/', \yii\helpers\Json::encode([
            'service_id' => '75',
            'CUsers_id' => 'ias_api_user',

            'City' => 'Москва',
            'Name' => 'TESST',
            'phone' => '11111111111',
            'Content' => 'TESST',
            'Address' => 'TESST',

            //'StartTime' => '11.09.2019 08:00',
            //'EndTime' => $endDate->format('d.m.Y H:i'),

            'fields' => [
                'Стоимость' => '32123',
                'Комментарий НСК' => 'Комментарий НСК',
            ]
        ]), 'ias_api_user', 'ias_test');

        //\yii\helpers\Json::decode($res);

//        die('<pre>'.print_r(\yii\helpers\Json::decode($res), true).'</pre>');

        return $this->renderContent($res);
    }

}















