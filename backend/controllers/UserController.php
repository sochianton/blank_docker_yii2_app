<?php

namespace backend\controllers;

use common\ar\User;
use common\controllers\CRUDController;
use common\ar\AuthItems;
use common\services\AuthItemService;
use Yii;
use yii\db\Exception;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class UserController extends CRUDController
{
    public $model = User::class;

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
//            'access' => [
//                'class' => AccessControl::class,
//                'rules' => [
//                    [
//                        'actions' => [
//                            'index',
//                            'update',
//                            'create',
//                            'delete',
//
//                            'roles',
//                            'create-role',
//                            'update-role',
//                            'delete-role',
//                            'role-children',
//                        ],
//                        'allow' => true,
//                        'roles' => ['@']
//                    ],
//                ],
//            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
                'only' => [
                    'role-children',
                    'ajax-search',
                ]
            ]
        ]);
    }

    public function init()
    {
        $this->indexTitle = Yii::t('app', 'Users');

        parent::init();
    }

    public function actionRoles(){

        $this->view->title = Yii::t('app', 'Roles and permissions');

        $this->view->params['breadcrumbs'] = [
            [
                'label' => Yii::t('app', 'Users'),
                'url' => ['index']
            ],
            $this->view->title,
        ];

        $model = new AuthItems([
            'scenario' => AuthItems::SCENARIO_SEARCH
        ]);

        return $this->render('roles', [
            'model' => $model
        ]);

    }

    /**
     * @return string|Response
     * @throws \Throwable
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionCreateRole(){

        $this->view->title = Yii::t('app', 'Create new role');

        $this->view->params['breadcrumbs'] = [
            [
                'label' => Yii::t('app', 'Users'),
                'url' => ['index']
            ],
            [
                'label' => Yii::t('app', 'Roles and permissions'),
                'url' => ['roles']
            ],
            $this->view->title,
        ];

        $model = new AuthItems([
            'scenario' => AuthItems::SCENARIO_CREATE
        ]);

        if(Yii::$app->request->isPost AND $model->load(Yii::$app->request->post()) AND AuthItemService::insert($model)){
            Yii::$app->session->setFlash('success', Yii::t('errors', 'Operation successfully done'));
            return $this->redirect(['roles']);
        }

        return $this->render('roleForm', [
            'model' => $model
        ]);

    }

    /**
     * @param $id
     * @return string
     * @throws \Throwable
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdateRole($id){

        $this->view->title = Yii::t('app', 'Update role');

        $this->view->params['breadcrumbs'] = [
            [
                'label' => Yii::t('app', 'Users'),
                'url' => ['index']
            ],
            [
                'label' => Yii::t('app', 'Roles and permissions'),
                'url' => ['roles']
            ],
            $this->view->title,
        ];

        $model = AuthItems::findOne($id);
        $model->setScenario(AuthItems::SCENARIO_UPDATE);



        if(Yii::$app->request->isPost){
            if($load = $model->load(Yii::$app->request->post()) AND AuthItemService::update($model)){
                Yii::$app->session->setFlash('success', Yii::t('errors', 'Operation successfully done'));
            }
        }


        return $this->render('roleForm', [
            'model' => $model
        ]);

    }

    /**
     * @param $id
     * @throws Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteRole($id){


        if(AuthItems::findOne($id)->delete()){
            Yii::$app->session->setFlash('success', Yii::t('errors', 'Operation successfully done'));
        }
        else{
            throw new Exception('Can not delete item');
        }

        return $this->redirect(['roles']);

    }

    /**
     * @param $id
     * @return array
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRoleChildren($id){

        $model = new AuthItems([
            'scenario' => AuthItems::SCENARIO_SEARCH
        ]);

        $model->parent = $id;

        $grid = AuthItems::getGrid($model);
        $grid->pager = null;

        $arr = $grid->generateJsonCols(0, true);

        return $arr;

    }



    public function actionAjaxSearch(){

        $params = Yii::$app->request->post('params', []);
        $user = new User();
        $params[$user->formName()] = $params;

        $provider = $user->search($params);

        $provider->pagination=false;

        return array_map(function (User $model){
            return [
                'id' => $model->id,
                'text' => $model->getFullName(),
            ];
        }, $provider->getModels());

//        $nodes = [
//            [
//                'id' => 10,
//                'text' => 'TEST1',
//            ],[
//                'id' => 11,
//                'text' => 'TEST2',
//            ],
//        ];
//
//
//        return $nodes;

    }

}
