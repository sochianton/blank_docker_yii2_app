<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\forms\CustomerForm */
/* @var $companies array */

$this->title = Yii::t('app', 'Update Customer: {id}', [
    'id' => '' . $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="customer-update">
    <?= $this->render('_form', [
        'model' => $model,
        'companies' => $companies,
    ]) ?>

</div>
