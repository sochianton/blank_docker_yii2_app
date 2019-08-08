<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model PasswordResetRequestForm */

use backend\models\forms\PasswordResetRequestForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Request password reset');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-request-password-reset">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

            <?= $form->field($model, 'email')->label(Yii::t('app', 'Email'))
                ->textInput(['autofocus' => true, 'onclick' => '$("#submitResetPassword").prop("disabled", false)']) ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Send'),
                    [
                        'class' => 'btn btn-primary',
                        'id' => 'submitResetPassword',
                        'onclick' => '$(this).prop("disabled", true).submit()'
                    ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
