<?php

namespace common\widgets;

use common\ar\AuthItems;
use kartik\grid\ActionColumn;
use yii\base\Model;
use yii\grid\Column;
use yii\web\View;

class AppGridView extends \kartik\grid\GridView
{

    public $box;
    public $pageSizes = [5=>5,10=>10,20=>20,30=>30,50=>50,100=>100];
    public $summaryOptions = [
        'class' => 'summary ',
        'tag' => 'span ',
    ];
    public $menu=[];
    public $parts = [];


    public function init()
    {
        parent::init();

        $this->initPageSizer();

        preg_replace_callback('/{\\w+}/', function ($matches) {
            $this->parts[] = $matches[0];
        }, $this->layout);

        if(!isset($this->pager['options']['class'])){
            $this->pager['options']['class'] = 'pagination pagination-sm no-margin pull-right';
        }

        $this->initLayouts();

    }

    public function initPageSizer(){
        if($this->pageSizes !== false){


            $pageParam = $this->dataProvider->getPagination()->pageSizeParam;
            $val = \Yii::$app->request->get($pageParam, $this->dataProvider->getPagination()->defaultPageSize);

            $this->pageSizes = \yii\helpers\Html::dropDownList('', $val, $this->pageSizes, [
                'onchange' => "onChangePageSize(event)",
                'data-page-param' => $pageParam,
            ]);

            $this->view->registerJs(<<<js
function onChangePageSize(e){
    var el = e.target;
    var pageParam = jQuery(el).attr('data-page-param');
    var val = jQuery(el).val();
    tools.addUrlParamAndRedirect(pageParam, val);
}

js
                , View::POS_END, 'appGridPageSizerJs');

        }
    }

    public function initLayouts(){
        if(isset($this->box)){
            $this->layout = $this->render('appGridViewBox', [
                'widget' => $this,
            ]);
        }
    }


    /**
     * Генерируем Json данные для вставки в тблицу для Дерева
     * @param $nodeColumnIdx
     * @param bool $forChildren
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function generateJsonCols($nodeColumnIdx, $forChildren=false){

        $res = [];

        /** @var AuthItems[] $models */
        $models = array_values($this->dataProvider->getModels());

        $provider = $this->dataProvider;
        $keys = $provider->getKeys();

        foreach ($models as $index => $model) {

            $key = $keys[$index];
            /* @var $column Column */
            $tmp = [];
            foreach ($this->columns as $idx => $column) {



                if($idx == $nodeColumnIdx){
                    $tmp['key'] = $key;
                    $tmp['title'] = $column->renderDataCell($model, $key, $index);

                    $tmp['folder'] = ($model->type == 1);
                    $tmp['lazy'] = true;

                    $tmp['children'] = !$model->children;

                }
                else{
                    if($forChildren AND $column instanceof ActionColumn){
                        $tmp['cols'][$idx] = '';
                    }
                    else{
                        $tmp['cols'][$idx] = $column->renderDataCell($model, $key, $index);
                    }

                }
            }

            $res[] = $tmp;
        }

        return $res;


    }

}