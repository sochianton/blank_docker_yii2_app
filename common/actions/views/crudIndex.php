<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 15.06.19
 * Time: 22:59
 *
 * @var \yii\web\View   $this
 * @var array           $pjax
 * @var \yii\base\Widget          $grid
 *
 */

use \yii\widgets\Pjax;

?>

<? if($pjax):?>
    <? Pjax::begin($pjax)?>
<? endif;?>

<? $grid->run()?>

<? if($pjax):?>
    <? Pjax::end()?>
<? endif;?>