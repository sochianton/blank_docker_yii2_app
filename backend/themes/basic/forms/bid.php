<?php

use common\ar\Bid;
use common\ar\BidAttachment;
use common\widgets\AppForm;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use kartik\widgets\FileInput;
use yii\web\View;

/**
 *
 * @var View                            $this
 * @var array                           $formConfig
 * @var AppForm                         $form
 * @var \common\ar\Bid                 $model
 *
 */

$customers=\common\services\UserService::getCustomerList();
$employees=\common\services\UserService::getEmployeeList();
$works=\common\services\WorkService::getListCategoried();

$customerPhotos=$model->customerPhotosArr;
$employeePhotos=$model->employeePhotosArr;
$files=$model->filesArr;
?>
<?php $form = AppForm::begin($formConfig); ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'customer_id')->dropDownList(array_map(function ($el) {
    return Yii::t('app', $el);
}, $customers), ['prompt' => '...']) ?>

<?= $form->field($model, 'employee_id')->dropDownList(array_map(function ($el) {
    return Yii::t('app', $el);
}, $employees), ['prompt' => '...']) ?>

<?= $form->field($model, 'status')->dropDownList(array_map(function ($el) {
    return Yii::t('app', $el);
}, Bid::STATUSES)) ?>

<?= $form->field($model, 'price')->textInput() ?>

<?= $form->field($model, 'object')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'complete_at')->widget(DateTimePicker::class, [
    'options' => [
        'value' => !empty($model->complete_at) ? Yii::$app->formatter->asDatetime($model->complete_at,
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
        'placeholder' => Yii::t('app', 'Select a works...'),
        'multiple' => true
    ],
]) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'customer_comment')->textarea() ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'employee_comment')->textarea() ?>
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
        'maxFileSize' => \common\models\BidAttachment::MAX_FILE_SIZE,
        'maxFileCount' => BidAttachment::MAX_FILES,
        'allowedFileExtensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'xlsm'],
        'deleteUrl' => '/bid/ajax-delete-file'
    ]
]); ?>

<?php AppForm::end(); ?>