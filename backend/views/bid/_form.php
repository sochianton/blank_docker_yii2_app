<?php

use common\models\Bid;
use common\models\BidAttachment;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use kartik\widgets\FileInput;
use scl\activeform\ActiveFormRequiredSave;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Bid */
/* @var $form yii\widgets\ActiveForm */
/* @var $customers array */
/* @var $employees array */
/* @var $works array */
/* @var $customerPhotos array */
/* @var $employeePhotos array */
/* @var $files array */
?>

<div class="bid-form">

    <?php $form = ActiveFormRequiredSave::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customerId')->dropDownList(array_map(function ($el) {
        return Yii::t('app', $el);
    }, $customers), ['prompt' => '...']) ?>

    <?= $form->field($model, 'employeeId')->dropDownList(array_map(function ($el) {
        return Yii::t('app', $el);
    }, $employees), ['prompt' => '...']) ?>

    <?= $form->field($model, 'status')->dropDownList(array_map(function ($el) {
        return Yii::t('app', $el);
    }, Bid::STATUSES)) ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'object')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'completeAt')->widget(DateTimePicker::class, [
        'options' => [
            'value' => !empty($model->completeAt) ? Yii::$app->formatter->asDatetime($model->completeAt,
                'php:Y-m-d H:i:s') : '',
            'placeholder' => Yii::t('app', 'Enter date...')
        ],
        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
        'removeButton' => false,
        'readonly' => true,
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd hh:ii:00',
            'todayHighlight' => true,
            'todayBtn' => true,
        ]
    ]); ?>

    <?= $form->field($model, 'works')->widget(Select2::class, [
        'data' => $works,
        'language' => 'ru',
        'options' => [
            'placeholder' => Yii::t('app', 'Select a works...')
        ],
    ])->label(Yii::t('app', 'Works')) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'customerComment')->textarea() ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'employeeComment')->textarea() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'customerPhotos[]')->widget(FileInput::class, [
                'options' => [
                    'multiple' => true,
                    'placeholder' => Yii::t('app', 'Select a photos...')
                ],
                'pluginOptions' => [
                    'initialPreviewAsData' => true,
                    'browseOnZoneClick' => true,
                    'showRemove' => true,
                    'showUpload' => false,
                    'initialPreview' => !empty($customerPhotos) ? array_column($customerPhotos, 'url') : [],
                    'initialPreviewConfig' => !empty($customerPhotos) ? array_map(function ($photo) {
                        return [
                            'type' => 'image',
                            'key' => $photo['path'],
                            'caption' => $photo['name'],
                            'downloadUrl' => true,
                        ];
                    }, $customerPhotos) : [],
                    'overwriteInitial' => false,
                    'maxFileSize' => BidAttachment::MAX_PHOTO_SIZE,
                    'maxFileCount' => BidAttachment::MAX_PHOTOS_CUSTOMER,
                    'allowedFileExtensions' => ['jpg', 'jpeg', 'png'],
                    'deleteUrl' => '/bid/ajax-delete-file'
                ]
            ]); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'employeePhotos[]')->widget(FileInput::class, [
                'options' => [
                    'multiple' => true,
                    'placeholder' => Yii::t('app', 'Select a photos...')
                ],
                'pluginOptions' => [
                    'initialPreviewAsData' => true,
                    'browseOnZoneClick' => true,
                    'showRemove' => true,
                    'showUpload' => false,
                    'initialPreview' => !empty($employeePhotos) ? array_column($employeePhotos, 'url') : [],
                    'initialPreviewConfig' => !empty($employeePhotos) ? array_map(function ($photo) {
                        return [
                            'type' => 'image',
                            'key' => $photo['path'],
                            'caption' => $photo['name'],
                            'downloadUrl' => true,
                        ];
                    }, $employeePhotos) : [],
                    'overwriteInitial' => false,
                    'maxFileSize' => BidAttachment::MAX_PHOTO_SIZE,
                    'maxFileCount' => BidAttachment::MAX_PHOTOS_EMPLOYEE,
                    'allowedFileExtensions' => ['jpg', 'jpeg', 'png'],
                    'deleteUrl' => '/bid/ajax-delete-file'
                ]
            ]); ?>
        </div>
    </div>

    <?= $form->field($model, 'files[]')->widget(FileInput::class, [
        'options' => [
            'multiple' => true,
            'placeholder' => Yii::t('app', 'Select a files...')
        ],
        'pluginOptions' => [
            'initialPreviewAsData' => true,
            'browseOnZoneClick' => true,
            'showRemove' => true,
            'showUpload' => false,
            'previewFileType' => 'any',
            'initialPreview' => !empty($files) ? array_column($files, 'url') : [],
            'initialPreviewConfig' => !empty($files) ? array_map(function ($file) {
                return [
                    'type' => strpos($file['name'], '.pdf') !== false ? 'pdf' : 'office',
                    'key' => $file['path'],
                    'caption' => $file['name'],
                    'downloadUrl' => $file['url'],
                ];
            }, $files) : [],
            'overwriteInitial' => false,
            'maxFileSize' => BidAttachment::MAX_FILE_SIZE,
            'maxFileCount' => BidAttachment::MAX_FILES,
            'allowedFileExtensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'xlsm'],
            'deleteUrl' => '/bid/ajax-delete-file'
        ]
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveFormRequiredSave::end(); ?>

</div>
