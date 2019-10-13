<?php

namespace common\widgets;

use \yii\base\Widget;
use yii\helpers\Html;

class BoxWidget extends Widget
{

    const TYPE_PRIMARY = 'box-primary';
    const TYPE_SUCCESS = 'box-success';
    const TYPE_WARNING = 'box-warning';
    const TYPE_DANGER = 'box-danger';

    public $type=false;
    public $options=[];
    public $headrOptions=[];
    public $bodyOptions=[];
    public $footerOptions=[];


    public $noHeaderBorder = false;
    public $noFooterBorder = false;
    public $noHeaderPadding = false;
    public $noBorderPadding = false;
    public $noFooterPadding = false;

    public $content;
    public $footer;

    public $title;
    public $layout='box';

    public $beginForm='';
    public $endForm='';

    public function init()
    {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        Html::addCssClass($this->options, 'box');
        Html::addCssClass($this->headrOptions, 'box-header');
        Html::addCssClass($this->bodyOptions, 'box-body');
        Html::addCssClass($this->footerOptions, 'box-footer');

        if($this->type !== false){
            Html::addCssClass($this->options, $this->type);
        }

        if(!$this->noHeaderBorder){
            Html::addCssClass($this->headrOptions, 'with-border');
        }

        if($this->noFooterBorder){
            Html::addCssClass($this->footerOptions, 'no-border');
        }

        if($this->noHeaderPadding){
            Html::addCssClass($this->headrOptions, 'no-padding');
        }

        if($this->noBorderPadding){
            Html::addCssClass($this->bodyOptions, 'no-padding');
        }

        if($this->noFooterPadding){
            Html::addCssClass($this->footerOptions, 'no-padding');
        }

        if(!$this->content){
            ob_start();
            ob_implicit_flush(false);
        }

    }

    public function run()
    {

        if(!$this->content){
            $content = ob_get_clean();
        }
        else{
            $content = $this->content;
        }


        return $this->render($this->layout, [
            'widget' => $this,
            'content' => $content,
        ]);
    }

}