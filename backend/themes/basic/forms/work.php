<?php

use common\widgets\AppForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 *
 * @var View                            $this
 * @var array                           $formConfig
 * @var AppForm                         $form
 * @var \common\ar\Work                 $model
 *
 */


?>
<?php $form = AppForm::begin($formConfig); ?>

<?=$form->errorSummary($model)?>

<?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>

<?= $form->field($model, 'price')->textInput() ?>

<?= $form->field($model, 'commission')->textInput(['type' => 'number', 'min' => 0, 'max' => 100, 'step' => 1]) ?>

<?= $form->field($model, 'qualifications')->widget(Select2::class, [
    'data' => ArrayHelper::map(\common\services\QualificationService::getList(), 'id', 'name'),
    'language' => 'ru',
    'options' => [
        'multiple' => false,
        'placeholder' => Yii::t('app', 'Select a category...'),
    ],
]) ?>

<?php AppForm::end(); ?>