<?php

use common\ar\User;
use common\widgets\AppForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\MaskedInput;
use \common\services\QualificationService;

/**
 *
 * @var View                            $this
 * @var array                           $formConfig
 * @var AppForm                         $form
 * @var \common\ar\User                 $model
 *
 */

\common\assets\FancyTreeAsset::register($this);
$treeUrl = \yii\helpers\Url::toRoute(['work/ajax-tree-work-nodes']);

?>
<?php if(!$model->isNewRecord):?>
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-money" style="margin-top: 15px;"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text"><?=$model->getAttributeLabel('balance')?></span>
                    <span class="info-box-number"><?=\Yii::$app->formatter->asCurrency($model->balance)?></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
    </div>
<?php endif;?>

<?php $form = AppForm::begin($formConfig); ?>

<?=$form->errorSummary($model)?>

<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

<?= $form->field($model, 'passwordRepeat')->passwordInput(['maxlength' => true]) ?>

<?= $form->field($model, 'phone')->widget(MaskedInput::class, [
    'mask' => '+7(999)-999-9999',
    'options' => [
        'class' => 'form-control',
        'placeholder' => Yii::t('app', 'Phone'),
    ],
    'clientOptions' => [
        'removeMaskOnSubmit' => true,
    ]
]) ?>

<?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'second_name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'type')->dropDownList(array_map(function ($el) {
    return Yii::t('app', $el);
}, User::TYPES)) ?>

<?= $form->field($model, 'status')->dropDownList(array_map(function ($el) {
    return Yii::t('app', $el);
}, User::STATUSES)) ?>

<?= $form->field($model, 'company_id')->dropDownList(\common\services\CompanyService::getListArr(), ['prompt' => '...']) ?>

<?= $form->field($model, 'formRoles')->widget(Select2::class, [
    'data' => \yii\helpers\ArrayHelper::map(\common\ar\AuthItems::getAllList()->all(), 'name', 'name'),
    'language' => 'ru',
    'options' => [
        'multiple' => true,
        'placeholder' => Yii::t('app', 'Select an option'),
    ],
]) ?>

<?//= $form->field($model, 'qualifications')->widget(Select2::class, [
//    'data' => ArrayHelper::map(QualificationService::getList(), 'id', 'name'),
//    'language' => 'ru',
//    'options' => [
//        'multiple' => true,
//        'placeholder' => Yii::t('app', 'Select a qualifications ...'),
//    ],
//])->label(Yii::t('app', 'Qualifications')) ?>

<div class="form-group highlight-addon field-user-qualifications">
    <label class="control-label col-md-2" ><?=$model->getAttributeLabel('works')?></label>
    <div class="col-md-10">
        <div id="Tree" style="height: 250px; overflow-y: scroll;"></div>
    </div>
</div>



<?= $form->field($model, 'formPhoto')->fileInput(); ?>
<? if($model->photo):?>
<?= Html::img($model->getPhotoUrl(), ['width' => '200px']);?>
<? endif;?>

<?php AppForm::end(); ?>


<?
$modelClass = $model->formName();
$this->registerJs(<<<JS
    jQuery('#{$form->id}').submit(function(e){
    
        jQuery("#Tree").fancytree("getTree").generateFormElements('{$modelClass}[works][]', true, {stopOnParents:false});
    
    });
    
    function setFancyTree(userId){
        
        if(!userId) userId = -1;
        
        jQuery("#Tree").fancytree({
            extensions: ["wide"],
            activeVisible: true,
            autoScroll: true,
            source: {
                url: "{$treeUrl}",
                cache: false
            },
            ajax: {
                data:{
                    user_id:userId
                }
            },
            checkbox: true,
            selectMode: 3,
            // icon: function(event, data){
            //
            //     if( data.node.data.isProject ) {
            //         return "icon-globe";
            //     }
            //
            // },
        });
    }
    
    setFancyTree({$model->id});
    
JS
);




























