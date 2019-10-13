<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 20.10.17
 * Time: 16:36
 */

/* @var $this \yii\web\View */

use yii\helpers\Html; ?>
<div id="FooterBox">
    <p class="pull-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>
</div>
