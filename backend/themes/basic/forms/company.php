<?php

use common\ar\Company;
use common\widgets\AppForm;
use yii\web\View;

/**
 *
 * @var View            $this
 * @var array           $formConfig
 * @var AppForm         $form
 * @var Company         $model
 *
 */


?>
<?php $form = AppForm::begin($formConfig); ?>

    <?=$form->errorSummary($model)?>

    <?= $form->field($model, 'type')->dropDownList(array_map(function ($el) {
        return Yii::t('app', $el);
    }, Company::TYPES)) ?>

    <?= $form->field($model, 'status')->dropDownList(array_map(function ($el) {
        return Yii::t('app', $el);
    }, Company::STATUSES)) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'number_of_contract')->textInput(['maxlength' => true]) ?>

<?php AppForm::end(); ?>
