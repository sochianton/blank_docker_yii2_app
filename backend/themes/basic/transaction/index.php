<?php

use backend\models\search\TransactionSearch;
use common\dto\TransactionDto;
use kartik\export\ExportMenu;
use kartik\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ArrayDataProvider */
/* @var $searchModel TransactionSearch */

$this->title = Yii::t('app', 'Transactions');
$this->params['breadcrumbs'][] = $this->title;
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    [
        'attribute' => 'date',
        'contentOptions' => ['style' => 'width: 13%; white-space: normal;'],
        'headerOptions' => ['style' => 'width: 13%; white-space: normal;'],
        'format' => 'dateTime',
        'label' => Yii::t('app', 'Date'),
    ],
    [
        'attribute' => 'bidId',
        'label' => Yii::t('app', 'Bid ID'),
        'content' => function (TransactionDto $transactionDto) {
            return Html::a($transactionDto->bidId, ['bid/view', 'id' => $transactionDto->bidId],
                ['target' => '_blank']);
        }
    ],
    [
        'attribute' => 'customer',
        'label' => Yii::t('app', 'Customer'),
    ],
    [
        'attribute' => 'employee',
        'label' => Yii::t('app', 'Employee'),
    ],
    [
        'attribute' => 'price',
        'label' => Yii::t('app', 'Price'),
    ],
    [
        'attribute' => 'commission',
        'label' => Yii::t('app', 'Commission'),
    ],
];
?>
<div class="bid-index">

    <div class="box box-default collapsed-box">
        <div class="box-header with-border">
            <h3 class="box-title"><?=Yii::t('app', 'Report')?></h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="box-body" style="display: none;">
            <div class="row transaction-search-block">
                <div class="col-md-7">
                    <?php $form = ActiveForm::begin([
                        'action' => ['index'],
                        'method' => 'get',
                        'options' => [
                            'data-role' => 'filters-form',
                        ]
                    ]); ?>
                    <div class="row">
                        <div class="col-md-5">
                            <?= $form->field($searchModel, 'dateStart')->widget(DateTimePicker::class, [
                                'options' => [
                                    'placeholder' => '     ...',
                                    'value' => !empty($searchModel->dateStart) ? Yii::$app->formatter->asDatetime($searchModel->dateStart,
                                        'php:Y-m-d H:i:s') : '',
                                    'autocomplete' => 'off'
                                ],
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd hh:ii:00',
                                    'todayHighlight' => true,
                                    'todayBtn' => true,
                                ]
                            ]) ?>
                        </div>
                        <div class="col-md-5">
                            <?= $form->field($searchModel, 'dateEnd')->widget(DateTimePicker::class, [
                                'options' => [
                                    'placeholder' => '     ...',
                                    'value' => !empty($searchModel->dateEnd) ? Yii::$app->formatter->asDatetime($searchModel->dateEnd,
                                        'php:Y-m-d H:i:s') : '',
                                    'autocomplete' => 'off'
                                ],
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd hh:ii:00',
                                    'todayHighlight' => true,
                                    'todayBtn' => true,
                                ]
                            ]) ?>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-default"><?= Yii::t('app', 'Create report') ?></button>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
                <div class="col-md-5" style="text-align: right;">
                    <?= ExportMenu::widget([
                        'folder' => '@runtime/export/',
                        'dataProvider' => $dataProvider,
                        'columns' => $gridColumns,
                        'target' => ExportMenu::TARGET_BLANK,
                        'clearBuffers' => true,
                        'deleteAfterSave' => true,
                        'showColumnSelector' => false,
                        'showConfirmAlert' => false,
                        'exportConfig' => [
                            ExportMenu::FORMAT_TEXT => false,
                            ExportMenu::FORMAT_HTML => false,
                            ExportMenu::FORMAT_PDF => false,
                            ExportMenu::FORMAT_EXCEL => false,
                            ExportMenu::FORMAT_CSV => false,
                        ],
                        'filename' => 'Transactions' . '_' . date('d-m-y', time()),
                        'dropdownOptions' => [
                            'label' => Yii::t('app', 'Export'),
                            'icon' => '',
                        ],
                    ]); ?>
                </div>
            </div>
        </div>

    </div>



    <?= \common\widgets\AppGridView::widget([
        'id' => 'grid_'.$searchModel->formName(),
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'box' => [
            'no-padding' => true,
        ],
        'menu' => [
            'options' => [
                'tag' => null,
            ],
            'itemOptions' => [
                'tag' => null,
            ],
            'encodeLabels' => false,
            'linkTemplate' => '<a class="btn btn-default" href="{url}">{label}</a>',
            'items' => [

                [
                    'label' => '{export}'
                ],
            ]
        ],
        'resizableColumns'=>true,
        'responsive'=>true,
        'responsiveWrap'=>true,
        'columns' => $gridColumns,
    ]); ?>


</div>
