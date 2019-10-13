<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 21.06.19
 * Time: 21:01
 *
 * @var $widget \common\widgets\AppGridView
 *
 */

//Yii::$app->_->d($widget->pjax);


?>
<div class="box">
    <? if($widget->caption OR ($widget->menu AND isset($widget->menu['items']) AND !empty($widget->menu['items']))):?>
        <div class="box-header with-border">

            <? if($widget->caption):?>
                <h3 class="box-title"><?=$widget->caption?></h3>
            <? endif;?>

            <?=\yii\widgets\Menu::widget($widget->menu)?>

        </div>
    <? endif;?>

    <div class="box-body <?=(isset($widget->box['no-padding'])?'no-padding':'')?>">
        <?=(in_array('{items}', $widget->parts)?'{items}':'')?>
    </div>


    <? if(in_array('{pager}', $widget->parts) OR in_array('{summary}', $widget->parts)):?>
        <div class="box-footer clearfix">
            <div class="row">
                <div class="col-sm-5">

                    <?=$widget->pageSizes?:''?>

                    <? if(in_array('{summary}', $widget->parts)):?>
                        {summary}
                    <? endif;?>
                </div>
                <div class="col-sm-7">
                    <? if(in_array('{pager}', $widget->parts)):?>
                        {pager}
                    <? endif;?>
                </div>
            </div>
        </div>
    <? endif;?>

</div>


