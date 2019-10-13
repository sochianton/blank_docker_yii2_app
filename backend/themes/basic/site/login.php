<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use common\widgets\AppForm as ActiveForm;

$field_config = \backend\controllers\SiteController::getFormFieldConfig();
?>
<div class="login-box-body">
    <p><?=Yii::t('app', 'Please fill out the following fields to login:')?></p>

    <div class="row">
        <div class="col-lg-12">
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'enableAjaxValidation' => false,
                'generateSbmtBtn' => false,
                'enableClientValidation' => false,
            ]); ?>

                <div class="has-feedback">
                    <?= $form->field($model, 'email', $field_config)->textInput(['autofocus' => true, 'placeholder' => Yii::t('app', 'Email')]) ?>
                    <span class="fa fa-user form-control-feedback"></span>
                </div>

                <div class="has-feedback">
                    <?= $form->field($model, 'password', $field_config)->passwordInput(['placeholder' => Yii::t('app', 'Password')]) ?>
                    <span class="fa fa-unlock-alt form-control-feedback"></span>
                </div>

                <?= $form->field($model, 'rememberMe')->checkbox()->label(Yii::t('app', 'Remember Me'), ['data-icheck'=>1])  ?>

                <hr/>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-3">
                        <?= Html::submitButton(Yii::t('app', 'Login'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
                    </div>

                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
