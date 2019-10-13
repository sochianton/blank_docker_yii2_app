<?php

/**
 * @var $this               \yii\web\View
 * @var $widget             \common\widgets\BoxWidget
 * @var $content             string
 *
 */

use yii\helpers\Html;

?>
<? $this->beginBlock('BoxWidgetBody')?>
    <?=Html::beginTag('div', $widget->bodyOptions)?>
        <?=$content?>
    <?=Html::endTag('div');?>
<? $this->endBlock() ?>

<? $this->beginBlock('BoxWidgetFooter')?>
    <?=Html::beginTag('div', $widget->footerOptions)?>
        <?=$widget->footer?>
    <?=Html::endTag('div');?>
<? $this->endBlock() ?>


<?=Html::beginTag('div', $widget->options)?>


    <? if(isset($widget->title)):?>
        <?=Html::beginTag('div', $widget->headrOptions)?>
            <h3 class="box-title"><?=$widget->title?></h3>
        <?=Html::endTag('div');?>
    <?endif;?>

    <?=$widget->beginForm?>
    <?= $this->blocks['BoxWidgetBody'] ?>
    <?= $this->blocks['BoxWidgetFooter'] ?>
    <?=$widget->endForm?>


<?=Html::endTag('div');
