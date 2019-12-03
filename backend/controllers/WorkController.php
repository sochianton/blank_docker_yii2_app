<?php

namespace backend\controllers;

use common\ar\Work;
use common\controllers\CRUDController;
use common\services\QualificationService;
use common\services\UserService;
use common\services\WorkService;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class WorkController extends CRUDController
{
    public $model = Work::class;

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
//                            'ajax-tree-work-nodes',
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
                    //'application/xml' => Response::FORMAT_XML,
                    'application/json' => Response::FORMAT_JSON,
                ],
                'only' => ['ajax-tree-work-nodes'],
            ],
        ]);
    }

    public function init()
    {
        $this->indexTitle = Yii::t('app', 'Works');

        parent::init();
    }

    /**
     * @param $user_id
     * @return array
     */
    public function actionAjaxTreeWorkNodes($user_id){


        $nodes = [];
        $works=[];
        if($user_id != -1) $works = UserService::getWorksIds($user_id);

        foreach (QualificationService::getList() as $cat){
            $tmp =[
                'title' => $cat->name,
                'folder' => true,
                'key' => '',
            ];
            $children = [];
            foreach (WorkService::getByCategoryIds($cat->id) as $work){
                $children[] = array(
                    'title' => $work->name,
                    'key' => $work->id,
                    //'selected' => (in_array($c->id, $selected) ? true : false),
                    'selected' => (in_array($work->id, $works)?true:false)
                );
            }

            if(!empty($children)){
                $tmp['children'] = $children;
            }

            $nodes[] = $tmp;

        }

        return $nodes;

    }
}
