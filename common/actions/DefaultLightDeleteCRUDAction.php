<?php

namespace common\actions;

use common\ar\AppActiveRecord;
use common\ar\Company;
use common\interfaces\BaseServiceInterface;
use common\interfaces\CRUDControllerModelInterface;
use Yii;
use yii\base\Model;
use yii\db\Expression;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;

/**
 * Created by PhpStorm.
 * User: anton
 * Date: 15.06.19
 * Time: 22:34
 */
class DefaultLightDeleteCRUDAction extends BaseAppAction
{

    const EVENT_BEFORE_DELETE = 'before_delete';

    public $model;
    public $attribute = 'deleted_at';


    /**
     * @return bool
     * @throws ServerErrorHttpException
     */
    protected function beforeRun()
    {
        if(parent::beforeRun()){

            if(!$this->model) throw new ServerErrorHttpException(Yii::t('errors', 'Need to define model property'));
            if(!$this->attribute) throw new ServerErrorHttpException(Yii::t('errors', 'Need to define deleteAttribute property'));

            if(!($this->model instanceof AppActiveRecord)) throw new ServerErrorHttpException('Need to implement '.AppActiveRecord::class);
            if(!($this->model instanceof CRUDControllerModelInterface)) throw new ServerErrorHttpException('Need to implement '.CRUDControllerModelInterface::class);

            return true;
        }
        return false;


    }

    /**
     * @throws HttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run(){

        $id = Yii::$app->request->get('id');
        if(!$id){
            throw new HttpException(Yii::t('errors','Need to define ID'));
        }

        /** @var Company $model */
        $model = $this->model;

        /** @var BaseServiceInterface $service */
        $service = $this->model->getService();

        /** @var Company $model */
        $model = $service::get($id);

        $this->trigger(self::EVENT_BEFORE_DELETE);

        $model->{$this->attribute} = new Expression('NOW()');

        if($model->save()){
            Yii::$app->session->setFlash('success', Yii::t('errors', 'Operation successfully done'));
        }
        else{
            Yii::$app->session->setFlash('error', Yii::t('errors', 'Can\'t delete record from db'));
        }

        $this->controller->redirect(['index']);

    }

}