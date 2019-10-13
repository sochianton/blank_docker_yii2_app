<?php

use common\models\Qualification;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\forms\EmployeeForm */
/* @var $companies array */
/* @var $qualifications Qualification[] */

$this->title = Yii::t('app', 'Update Employee: {id}', [
    'id' => '' . $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Employees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="employee-update">
    <?= $this->render('_form', [
        'model' => $model,
        'companies' => $companies,
        'qualifications' => $qualifications,
    ]) ?>

</div>
