<?php


namespace common\controllers;


use common\actions\DefaultCreateAction;
use common\actions\DefaultDeleteCRUDAction;
use common\actions\DefaultIndexCRUDAction;
use common\actions\DefaultLightDeleteCRUDAction;
use common\actions\DefaultUpdateAction;
use common\ar\Company;
use common\interfaces\CRUDControllerModelInterface;
use Yii;
use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

class CRUDController extends AppController
{

    public $model=null;

    public $indexTitle;
    public $createTitle;
    public $updateTitle;

    public $indexBreadcrumbs;
    public $createBreadcrumbs;
    public $updateBreadcrumbs;

    public function init()
    {
        parent::init();

        if(!$this->createTitle) $this->createTitle = Yii::t('app', 'Create new record');
        if(!$this->updateTitle) $this->updateTitle = Yii::t('app', 'Update record');

    }

    /**
     * @param $action
     * @return bool
     * @throws ServerErrorHttpException
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {

        if(parent::beforeAction($action)){

            if(!$this->model) throw new ServerErrorHttpException(Yii::t('errors', 'Need to define model property'));
            $model = $this->_getModel();

            if(!($model instanceof CRUDControllerModelInterface)) throw new ServerErrorHttpException('Need to implement '.CRUDControllerModelInterface::class);

            return true;
        }
        return false;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actions()
    {

        return [
            'index' => [
                'class' => DefaultIndexCRUDAction::class,
                'model' => $this->_getModel(),
                'title' => $this->_getTitle('index'),
                'breadcrumbs' => $this->_getBreadcrumbs('index'),
            ],
            'create' => [
                'class' => DefaultCreateAction::class,
                'model' => $this->_getModel(),
                'title' => $this->_getTitle('create'),
                'breadcrumbs' => $this->_getBreadcrumbs('create'),
            ],
            'update' => [
                'class' => DefaultUpdateAction::class,
                'model' => $this->_getModel(),
                'title' => $this->_getTitle('update'),
                'breadcrumbs' => $this->_getBreadcrumbs('update'),
            ],
            'delete' => [
                'class' => DefaultLightDeleteCRUDAction::class,
                'model' => $this->_getModel(),
            ]
        ];
    }


    // ========

    protected $_model = null;

    /**
     * @return ActiveRecord
     */
    protected function _getModel(){

        if($this->_model === null)  $this->_model = new $this->model();

        return $this->_model;

    }

    /**
     * @param null $id
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    protected function _getTitle($id)
    {
        switch ($id) {
            case 'index' :
                return $this->indexTitle ?: $this->_getModel()->formName();
                break;
            case 'create' :
                return $this->createTitle ?: $this->_getModel()->formName();
                break;
            case 'update' :
                return $this->updateTitle ?: $this->_getModel()->formName();
                break;
            default :
                return $this->_getModel()->formName();
                break;
        }
    }

    /**
     * @param null $id
     * @return array|mixed|string
     * @throws \yii\base\InvalidConfigException
     */
    protected function _getBreadcrumbs($id){

        switch ($id) {
            case 'index' :
                if($this->indexBreadcrumbs) return $this->indexBreadcrumbs;
                else{
                    return [
                        $this->_getTitle('index'),
                    ];
                }
                break;
            case 'create' :
                if($this->createBreadcrumbs) return $this->createBreadcrumbs;
                else{
                    return [
                        ['label' => $this->_getTitle('index'), 'url' => ['index']],
                        $this->_getTitle('create'),
                    ];
                }
                break;
            case 'update' :
                if($this->updateBreadcrumbs) return $this->updateBreadcrumbs;
                else{
                    return [
                        ['label' => $this->_getTitle('index'), 'url' => ['index']],
                        $this->_getTitle('update'),
                    ];
                }
                break;
        }
        return [];
    }

    /**
     * @return array|\common\widgets\AppGridView|object
     * @throws \yii\base\InvalidConfigException
     */
    protected function _getGrid(){

        /** @var Company $model */
        $model = $this->_getModel();
        return $model::getGridWidget($model->search(Yii::$app->request->queryParams), $model);

    }

}