<?php

/**
 *
 * @var View                            $this
 * @var array                           $formConfig
 * @var AppForm                         $form
 * @var \common\ar\AuthItems                 $model
 *
 */

use common\widgets\AppForm;
use kartik\select2\Select2;
use yii\web\View;

?>
<?php $form = AppForm::begin([
    'id' => $model->formName(),
    'type' => AppForm::TYPE_HORIZONTAL,
    'box' => [],
]); ?>

<?=$form->errorSummary($model)?>


<?= $form->field($model, 'type')->widget(Select2::class, [
    'data' => \common\ar\AuthItems::getRoleTypesList(),
    'language' => 'ru',
    'options' => [
        'multiple' => false,
        'placeholder' => Yii::t('app', 'Select an option'),
    ],
]) ?>


<?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>

<?= $form->field($model, 'description')->textarea(['maxlength' => 500]) ?>

<?= $form->field($model, 'childrenForm')->widget(Select2::class, [
    'data' => \yii\helpers\ArrayHelper::map($model::getAllList($model->name)->all(), 'name', 'name'),
    'language' => 'ru',
    'options' => [
        'multiple' => true,
        'placeholder' => Yii::t('app', 'Select an option'),
    ],
]) ?>


<?php AppForm::end(); ?>

