<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Bid */
/* @var $customers array */
/* @var $employees array */
/* @var $works array */

$this->title = Yii::t('app', 'Create Bid');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bids'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bid-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'customers' => $customers,
        'employees' => $employees,
        'works' => $works,
        'customerPhotos' => [],
        'employeePhotos' => [],
        'files' => [],
    ]) ?>

</div>
