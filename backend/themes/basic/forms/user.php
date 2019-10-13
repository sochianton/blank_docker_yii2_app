<?php

use common\ar\User;
use common\widgets\AppForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\MaskedInput;
use \common\services\QualificationService;

/**
 *
 * @var View                            $this
 * @var array                           $formConfig
 * @var AppForm                         $form
 * @var \common\ar\User                 $model
 *
 */


?>
<?php $form = AppForm::begin($formConfig); ?>

<?=$form->errorSummary($model)?>

<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

<?= $form->field($model, 'passwordRepeat')->passwordInput(['maxlength' => true]) ?>

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

<?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'second_name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'type')->dropDownList(array_map(function ($el) {
    return Yii::t('app', $el);
}, User::TYPES)) ?>

<?= $form->field($model, 'balance')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'status')->dropDownList(array_map(function ($el) {
    return Yii::t('app', $el);
}, User::STATUSES)) ?>

<?= $form->field($model, 'company_id')->dropDownList(\common\services\CompanyService::getListArr(), ['prompt' => '...']) ?>

<?= $form->field($model, 'qualifications')->widget(Select2::class, [
    'data' => ArrayHelper::map(QualificationService::getList(), 'id', 'name'),
    'language' => 'ru',
    'options' => [
        'multiple' => true,
        'placeholder' => Yii::t('app', 'Select a qualifications ...'),
    ],
])->label(Yii::t('app', 'Qualifications')) ?>

<?= $form->field($model, 'formPhoto')->fileInput(); ?>
<? if($model->photo):?>
<?= Html::img($model->getPhotoUrl(), ['width' => '200px']);?>
<? endif;?>

<?php AppForm::end(); ?>