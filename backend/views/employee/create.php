<?php

use common\models\Qualification;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Employee */
/* @var $companies array */
/* @var $qualifications Qualification[] */

$this->title = Yii::t('app', 'Create Employee');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Employees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'companies' => $companies,
        'qualifications' => $qualifications,
    ]) ?>

</div>
