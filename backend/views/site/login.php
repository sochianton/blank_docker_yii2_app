<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \common\models\LoginForm */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Yii::t('app', 'Please fill out the following fields to login:') ?></p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'email')->textInput(['autofocus' => true])->label(Yii::t('app', 'Email')) ?>

            <?= $form->field($model, 'password')->passwordInput()->label(Yii::t('app', 'Password')) ?>

            <?= $form->field($model, 'rememberMe')->checkbox()->label(Yii::t('app', 'Remember Me')) ?>

            <div style="color:#999;margin:1em 0">
                <?= Html::a(Yii::t('app', 'Reset password'), ['site/request-password-reset']) ?>.
            </div>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Login'),
                    ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
