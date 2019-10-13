<?php

namespace common\actions;

use common\ar\AppActiveRecord;
use common\interfaces\BaseServiceInterface;
use common\interfaces\CRUDControllerModelInterface;
use Yii;
use yii\web\ServerErrorHttpException;

/**
 * Created by PhpStorm.
 * User: anton
 * Date: 15.06.19
 * Time: 22:34
 */
class DefaultCreateAction extends BaseAppAction
{

    const EVENT_BEFORE_RENDER = 'before_render';

    /** @var AppActiveRecord */
    public $model;

    public $title = '';
    public $breadcrumbs = [];

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

        /** @var BaseServiceInterface $service */
        $service = $this->model->getService();
        $this->model->setScenario(AppActiveRecord::SCENARIO_CREATE);

        if(\Yii::$app->request->isPost AND $this->model->load(\Yii::$app->request->post()) AND $this->model->validate()){

            if($service::insert($this->model)){
                Yii::$app->session->setFlash('success', Yii::t('errors', 'Operation successfully done'));
                return $this->controller->redirect(['update', 'id' => $this->model->id]);
            }

        }

        $this->trigger(self::EVENT_BEFORE_RENDER);
        return $this->controller->renderContent($this->model->getForm());

    }

}