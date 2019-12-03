<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \backend\models\forms\QualificationCreateForm */

$this->title = Yii::t('app', 'Update Category: {nameAttribute}', [
    'nameAttribute' => '' . $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="location-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'isNewRecord' => false,
    ]) ?>

</div>
