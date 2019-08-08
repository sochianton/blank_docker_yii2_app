<?php

use backend\models\forms\QualificationCreateForm;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model QualificationCreateForm */

$this->title = Yii::t('app', 'Create Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qualification-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'isNewRecord' => true,
    ]) ?>

</div>
