<?php

use backend\models\forms\QualificationUpdateForm;
use common\models\Qualification;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model QualificationUpdateForm */
/* @var $qualifications Qualification[] */

$this->title = Yii::t('app', 'Update Work: {nameAttribute}', [
    'nameAttribute' => '' . $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Works'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="location-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'qualifications' => $qualifications,
        'isNewRecord' => false,
    ]) ?>

</div>
