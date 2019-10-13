<?php

use backend\models\forms\CompanyForm;
use common\models\Company;
use scl\activeform\ActiveFormRequiredSave;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model CompanyForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-form">

    <?php $form = ActiveFormRequiredSave::begin(); ?>

    <?= $form->field($model, 'type')->dropDownList(array_map(function ($el) {
        return Yii::t('app', $el);
    }, Company::TYPES)) ?>

    <?= $form->field($model, 'status')->dropDownList(array_map(function ($el) {
        return Yii::t('app', $el);
    }, Company::STATUSES)) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'numberOfContract')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveFormRequiredSave::end(); ?>

</div>
