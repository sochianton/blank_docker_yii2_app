<?php

use common\models\Bid;
use kartik\daterange\DateRangePicker;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel backend\models\search\BidSearch */

$this->title = Yii::t('app', 'Bids');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bid-index">

    <? \common\ar\Bid::getGridWidget($dataProvider, $searchModel)->run()?>

<!--    --><?//= GridView::widget([
//        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
//        'rowOptions' => function ($model) {
//            return [
//                'style' => 'background-color: ' . (!empty($model->deleted_at) ? '#b36c6c' : '')
//            ];
//        },
//        'columns' => [
//            'id',
//            'name',
//            'customer_id',
//            'employee_id',
//            [
//                'attribute' => 'status',
//                'filter' => array_map(function ($el) {
//                    return Yii::t('app', $el);
//                }, Bid::STATUSES),
//                'value' => function (Bid $model) {
//                    return Yii::t('app', Bid::STATUSES[$model->status] ?? null);
//                }
//            ],
//            'price',
//            'object',
//            [
//                'attribute' => 'completeAt',
//                'label' => Yii::t('app', 'Completion date'),
//                'format' => 'date',
//                'value' => function (Bid $model) {
//                    return $model->complete_at;
//                },
//                'filter' => DateRangePicker::widget([
//                    'model' => $searchModel,
//                    'attribute' => 'completeAt',
//                    'convertFormat' => true,
//                    'hideInput' => false,
//                    'presetDropdown' => false,
//                    'pluginOptions' => [
//                        'removeButton' => [
//                            'icon' => 'trash',
//                        ],
//                        'timePicker' => false,
//                        'timePicker24Hour' => true,
//                        'timePickerIncrement' => 5,
//                        'locale' => [
//                            'format' => 'd-m-Y',
//                            'autoclose' => true,
//                        ],
//                        'opens' => 'left',
//                    ],
//                    'pluginEvents' => [
//                        'apply.daterangepicker' => "function(ev, picker) {
//                            $(picker.element).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY')).trigger('change')
//                       }",
//                    ]
//                ]),
//            ],
//            [
//                'attribute' => 'createdAt',
//                'label' => Yii::t('app', 'Creation date'),
//                'format' => 'date',
//                'value' => function (Bid $model) {
//                    return $model->created_at;
//                },
//                'filter' => DateRangePicker::widget([
//                    'model' => $searchModel,
//                    'attribute' => 'createdAt',
//                    'convertFormat' => true,
//                    'hideInput' => false,
//                    'presetDropdown' => false,
//                    'pluginOptions' => [
//                        'removeButton' => [
//                            'icon' => 'trash',
//                        ],
//                        'timePicker' => false,
//                        'timePicker24Hour' => true,
//                        'timePickerIncrement' => 5,
//                        'locale' => [
//                            'format' => 'd-m-Y',
//                            'autoclose' => true,
//                        ],
//                        'opens' => 'left',
//                    ],
//                    'pluginEvents' => [
//                        'apply.daterangepicker' => "function(ev, picker) {
//                            $(picker.element).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY')).trigger('change')
//                       }",
//                    ]
//                ]),
//            ],
//            [
//                'class' => yii\grid\ActionColumn::class,
//                'template' => '{view} {update} {block} {restore}',
//                'buttons' => [
//                    'view',
//                    'update',
//                    'block' => function (string $url, Bid $model) {
//                        return Html::a('<span class="fas fa-lock"></span>', ['block', 'id' => $model->id], [
//                            'title' => Yii::t('app', 'Block'),
//                            'class' => '',
//                            'data' => [
//                                'confirm' => Yii::t('app', 'You want to block this bid. Are you sure?'),
//                                'method' => 'post',
//                            ],
//                        ]);
//                    },
//                    'restore' => function ($url) {
//                        return Html::a('<span class="fas fa-lock-open"></span>', $url, [
//                            'title' => Yii::t('app', 'Restore'),
//                        ]);
//                    },
//                ],
//                'visibleButtons' => [
//                    'block' => function ($model) {
//                        return false;//return !(bool)$model->deleted_at;
//                    },
//                    'restore' => function ($model) {
//                        return false;//return (bool)$model->deleted_at;
//                    },
//                ]
//            ],
//        ],
//    ]); ?>
</div>
