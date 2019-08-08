<?php

use backend\models\forms\QualificationCreateForm;
use scl\activeform\ActiveFormRequiredSave;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model QualificationCreateForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $isNewRecord bool */

?>

<div class="qualification-form">

    <?php $form = ActiveFormRequiredSave::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>

    <?= $isNewRecord ? null : $form->field($model, 'is_deleted')->checkbox(['label' => Yii::t('app', 'Blocked')]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveFormRequiredSave::end(); ?>

</div>
