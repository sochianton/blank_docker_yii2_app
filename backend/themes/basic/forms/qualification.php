<?php

use common\widgets\AppForm;
use yii\web\View;

/**
 *
 * @var View            $this
 * @var array           $formConfig
 * @var AppForm         $form
 * @var \common\ar\Qualification         $model
 *
 */


?>
<?php $form = AppForm::begin($formConfig); ?>

<?=$form->errorSummary($model)?>

<?=$form->field($model, 'name')->textInput(['maxlength' => 100]) ?>

<?php AppForm::end(); ?>
