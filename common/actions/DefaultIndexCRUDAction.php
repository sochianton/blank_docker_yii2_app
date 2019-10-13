<?php

namespace common\actions;

use common\ar\AppActiveRecord;
use common\ar\Company;
use common\interfaces\CRUDControllerModelInterface;
use Yii;
use yii\web\ServerErrorHttpException;

/**
 * Created by PhpStorm.
 * User: anton
 * Date: 15.06.19
 * Time: 22:34
 */
class DefaultIndexCRUDAction extends BaseAppAction
{

    const EVENT_BEFORE_RENDER = 'before_render';

    /** @var Company */
    public $model='';
    public $pjax=false;

    public $title = '';
    public $breadcrumbs = [];
    public $viewPath = '@common/actions/views/crudIndex';

    /**
     * @return bool
     * @throws ServerErrorHttpException
     */
    protected function beforeRun()
    {
        if(parent::beforeRun()){

            if(!($this->model instanceof AppActiveRecord)) throw new ServerErrorHttpException('Need to implement '.AppActiveRecord::class);
            if(!($this->model instanceof CRUDControllerModelInterface)) throw new ServerErrorHttpException('Need to implement '.CRUDControllerModelInterface::class);

            return true;
        }
        return false;
    }

    public function run(){

        $this->controller->view->title = $this->title;
        $this->controller->view->params['breadcrumbs'] = $this->breadcrumbs;

        $this->trigger(self::EVENT_BEFORE_RENDER);

        $model = $this->model;
        $grid = $model::getGridWidget($model->search(Yii::$app->request->queryParams), $model);

        return $this->controller->render($this->viewPath, [
            'grid' => $grid,
            'pjax' => $this->pjax,
        ]);

    }

}