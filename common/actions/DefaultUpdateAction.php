<?php

namespace common\actions;

use common\ar\AppActiveRecord;
use common\ar\Company;
use common\interfaces\BaseServiceInterface;
use common\interfaces\CRUDControllerModelInterface;
use Yii;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;

/**
 * Created by PhpStorm.
 * User: anton
 * Date: 15.06.19
 * Time: 22:34
 */
class DefaultUpdateAction extends BaseAppAction
{

    const EVENT_BEFORE_RENDER = 'before_render';

    public $model;

    public $title = '';
    public $breadcrumbs = [];

    protected $_id;

    /**
     * @return bool
     * @throws HttpException
     * @throws ServerErrorHttpException
     */
    protected function beforeRun()
    {
        if(parent::beforeRun()){

            if(!($this->model instanceof AppActiveRecord)) throw new ServerErrorHttpException('Need to implement '.AppActiveRecord::class);
            if(!($this->model instanceof CRUDControllerModelInterface)) throw new ServerErrorHttpException('Need to implement '.CRUDControllerModelInterface::class);

            $id = Yii::$app->request->get('id');
            if(!$id){
                throw new HttpException(Yii::t('errors','Need to define ID'));
            }
            else{
                $this->_id = $id;
            }

            return true;
        }
        return false;
    }

    /**
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function run(){

        /** @var Company $model */
        $model = $this->model;

        /** @var BaseServiceInterface $service */
        $service = $model::getService();

        /** @var Company $model */
        $model = $service::get($this->_id);

        $this->controller->view->title = $this->title;
        $this->controller->view->params['breadcrumbs'] = $this->breadcrumbs;
        $model->setScenario(AppActiveRecord::SCENARIO_UPDATE);
        if(\Yii::$app->request->isPost AND $model->load(\Yii::$app->request->post()) AND $model->validate()){

            if($service::update($model)){
                Yii::$app->session->setFlash('success', Yii::t('errors', 'Operation successfully done'));
                return $this->controller->redirect(['update', 'id' => $model->id]);
            }

        }

        $this->trigger(self::EVENT_BEFORE_RENDER);
        return $this->controller->renderContent($model->getForm());

    }

}