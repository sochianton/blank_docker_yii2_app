<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Bid */
/* @var $customers array */
/* @var $employees array */
/* @var $works array */
/* @var $customerPhotos array */
/* @var $employeePhotos array */
/* @var $files array */

$this->title = Yii::t('app', 'Update Bid: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bids'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="bid-update">

    <?= $this->render('_form', [
        'model' => $model,
        'customers' => $customers,
        'employees' => $employees,
        'works' => $works,
        'customerPhotos' => $customerPhotos,
        'employeePhotos' => $employeePhotos,
        'files' => $files,
    ]) ?>

</div>
