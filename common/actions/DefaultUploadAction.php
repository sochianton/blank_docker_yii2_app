<?php

namespace common\actions;

use common\ar\AppActiveRecord;
use common\ar\Bid;
use common\ext\XLSXReader\XLSXReader;
use common\interfaces\BaseServiceInterface;
use common\interfaces\CRUDControllerModelInterface;
use common\interfaces\ImportRecordInterface;
use Yii;
use yii\db\Exception;
use yii\web\ServerErrorHttpException;

/**
 * Created by PhpStorm.
 * User: anton
 * Date: 15.06.19
 * Time: 22:34
 */
class DefaultUploadAction extends BaseAppAction
{

    const EVENT_BEFORE_RENDER = 'before_render';

    /** @var Bid */
    public $model='';

    public $title = '';
    public $breadcrumbs = [];
    public $viewPath = '@common/actions/views/upload';

    /**
     * @return bool
     * @throws ServerErrorHttpException
     */
    protected function beforeRun()
    {
        if(parent::beforeRun()){

            if(!($this->model instanceof AppActiveRecord)) throw new ServerErrorHttpException('Need to implement '.AppActiveRecord::class);
            if(!($this->model instanceof CRUDControllerModelInterface)) throw new ServerErrorHttpException('Need to implement '.CRUDControllerModelInterface::class);
            if(!($this->model instanceof ImportRecordInterface)) throw new ServerErrorHttpException('Need to implement '.ImportRecordInterface::class);

            return true;
        }
        return false;
    }

    public function run(){

        $this->controller->view->title = $this->title;
        $this->controller->view->params['breadcrumbs'] = $this->breadcrumbs;

        $model = $this->model;
        $header = $model::importHeader();
        if(!$header){
            $header = array_keys($model->attributes);
        }
        $header = array_filter($header, function ($val){
            return !in_array(mb_strtolower($val), ['id', 'del']);
        });
        $new_header = ['id'];
        foreach ($header as $h) $new_header[] = $h;
        $new_header[] = 'del';

        $headerDescription = $model::importHeaderDescription();

        $mapHeader = [];
        $grid = [];
        $errors = [];

        if(isset($_FILES['file'])){

            $file = $_FILES['file']['tmp_name'];

            if(file_exists($file)){
                try{
                    $xlsx = new XLSXReader($file);
                    $grid = $xlsx->getSheetData(1);

                    $row = $grid[0];

                    foreach ($new_header as $h){
                        foreach ($row as $ids=>$val){
                            if($val == $model->getAttributeLabel($h)){
                                $mapHeader[$h] = $ids;
                                break;
                            }
                        }
                    }
                }
                catch (Exception $e){
                    $errors[] = Yii::t('main-ui', 'Try format XLSX');
                }
            }


        }

        elseif(isset($_POST['Import']) AND !empty($_POST['Import'])){
            $data = $_POST['Import'];

            $grid = [null];

            /** @var Bid[] $save */
            $save = [];
            /** @var Bid[] $delete */
            $delete = [];
            foreach ($data as $idx => $item){


                $head = array_keys($item);
                $mapHeader = array_flip($head);

                $grid[] = array_values($item);

                if(isset($item['id'])){
                    $id = (int)$item['id'];
                    $record = $model::findOne($id);
                    if(!$record){
                        $record = new $model();
                    }
                }
                else{
                    $record = new $model();
                }

                if(isset($item['del']) AND (bool)$item['del'] AND !$record->isNewRecord){
                    //$record->delete();
                    $delete[] = $record;
                }
                else{
                    $item = $this->transformData($item, $model);
                    $record->setAttributes($item);
                    if(!$record->validate()){
                        $errors['Строка: '.$idx] = $record->errors;
                    }
                    else{
                        $save[] = $record;
                    }
                }

            }

//            die('<pre>'.print_r($save, true).'</pre>');

            if(empty($errors)){

                /** @var BaseServiceInterface $service */
                $service = $model->getService();

                //die('<pre>'.print_r($errors, true).'</pre>');

                $res = true;
                $transaction = Yii::$app->db->beginTransaction();



                foreach ($delete as $rec){
                    $res = ((bool)$rec->delete() AND $res);
                }
                foreach ($save as $rec){

                    if($rec->isNewRecord){
                        $rec->setScenario($model::SCENARIO_CREATE);
                        try{
                            $service::insert($rec, false);
                        }
                        catch (\yii\base\Exception $e){
                            $errors[] = $e->getMessage();
                            $res = false;
                        }

                    }
                    else{
                        $rec->setScenario($model::SCENARIO_UPDATE);
                        try{
                            $service::update($rec, false);
                        }
                        catch (\yii\base\Exception $e){
                            $errors[] = $e->getMessage();
                            $res = false;
                        }

                    }
                }

                if($res){
                    Yii::$app->session->setFlash('success', Yii::t('errors', 'Operation successfully done'));
                    $transaction->commit();
                }
                else{
                    $transaction->rollBack();
                    $errors[] = Yii::t('errors', 'Can\'t save records');
                }
            }

        }



        $this->trigger(self::EVENT_BEFORE_RENDER);
        return $this->controller->render($this->viewPath, [
            'model' => $model,
            'errors' => $errors,
            'header' => $new_header,
            'headerDescription' => $headerDescription,
            'mapHeader' => $mapHeader,
            'grid' => $grid,
        ]);

    }

    /**
     * @param $item
     * @param Bid $model
     * @return mixed
     */
    protected function transformData($item, $model){

        $map = $model::importAttributeRules();

        foreach ($item as $attr=>&$val){

            if(isset($map[$attr])){

                if(is_callable($map[$attr])){
                    $val = call_user_func($map[$attr], $val);
                }

            }

        }

        return $item;

    }

}