<?php

use common\ar\Company;
use common\widgets\AppForm;
use yii\web\View;

/**
 *
 * @var View            $this
 * @var array           $formConfig
 * @var AppForm         $form
 * @var \common\ar\Transactions         $model
 *
 */

\common\assets\Select2Asset::register($this);
$this->registerJs($model->formScripts());


$toData = [];
if($model->to){
    /** @var \common\ar\User $user */
    $user = \common\services\UserService::get($model->to);
    $toData = [
        $model->to => $user->getFullName()
    ];
}
?>
<?php $form = AppForm::begin($formConfig); ?>

    <?=$form->errorSummary($model)?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'to')->dropDownList($toData,['maxlength' => true]) ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

<?php AppForm::end(); ?>
