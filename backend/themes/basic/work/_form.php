<?php

use backend\models\forms\WorkCreateForm;
use common\models\Qualification;
use common\models\Work;
use kartik\select2\Select2;
use scl\activeform\ActiveFormRequiredSave;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model WorkCreateForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $qualifications Qualification[] */
/* @var $isNewRecord bool */

?>

<div class="qualification-form">

    <?php $form = ActiveFormRequiredSave::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'commission')->textInput(['type' => 'number', 'min' => 0, 'max' => 100, 'step' => 1]) ?>

    <?= $form->field($model, 'qualifications')->widget(Select2::class, [
        'data' => ArrayHelper::map($qualifications, 'id', 'name'),
        'language' => 'ru',
        'options' => [
            'multiple' => false,
            'placeholder' => Yii::t('app', 'Select a category...'),
        ],
    ])->label(Yii::t('app', 'Categories')) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveFormRequiredSave::end(); ?>

</div>
