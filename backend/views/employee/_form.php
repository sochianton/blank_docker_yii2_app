<?php

use common\models\Qualification;
use common\models\User;
use kartik\select2\Select2;
use scl\activeform\ActiveFormRequiredSave;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\Employee */
/* @var $form yii\widgets\ActiveForm */
/* @var $qualifications Qualification[] */
/* @var $companies array */
?>

<div class="employee-form">

    <?php $form = ActiveFormRequiredSave::begin(); ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->widget(MaskedInput::class, [
        'mask' => '+7(999)-999-9999',
        'options' => [
            'class' => 'form-control',
            'placeholder' => Yii::t('app', 'Phone'),
        ],
        'clientOptions' => [
            'removeMaskOnSubmit' => true,
        ]
    ]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'secondName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lastName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'balance')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'passwordRepeat')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(array_map(function ($el) {
        return Yii::t('app', $el);
    }, User::STATUSES)) ?>

    <?= $form->field($model, 'companyId')->dropDownList($companies, ['prompt' => '...']) ?>

    <?= $form->field($model, 'photo')->fileInput()->label(Yii::t('app', 'Photo')); ?>

    <?= $form->field($model, 'qualifications')->widget(Select2::class, [
        'data' => ArrayHelper::map($qualifications, 'id', 'name'),
        'language' => 'ru',
        'options' => [
            'multiple' => true,
            'placeholder' => Yii::t('app', 'Select a qualifications ...'),
        ],
    ])->label(Yii::t('app', 'Qualifications')) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveFormRequiredSave::end(); ?>

</div>
