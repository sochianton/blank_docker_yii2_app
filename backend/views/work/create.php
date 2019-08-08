<?php

use backend\models\forms\QualificationCreateForm;
use common\models\Qualification;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model QualificationCreateForm */
/* @var $qualifications Qualification[] */

$this->title = Yii::t('app', 'Create Work');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Works'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="work-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'qualifications' => $qualifications,
        'isNewRecord' => true,
    ]) ?>

</div>
