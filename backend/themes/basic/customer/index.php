<?php

use backend\models\search\CustomerSearch;
use common\models\Customer;
use common\widgets\AppGridView;
use kartik\daterange\DateRangePicker;
use kartik\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel CustomerSearch */

$this->title = Yii::t('app', 'Customers');
$this->params['breadcrumbs'][] = $this->title;

$cols = [
    [
        'attribute' => 'id',
        'headerOptions' => ['style' => 'width: 30px']
    ],
    'email:email',
    'phone',
    [
        'attribute' => 'name',
        'label' => Yii::t('app', 'First Name'),
        'value' => function (Customer $model) {
            return $model->first_name;
        },
    ],
    [
        'attribute' => 'secondName',
        'label' => Yii::t('app', 'Second Name'),
        'value' => function (Customer $model) {
            return $model->second_name;
        },
    ],
    [
        'attribute' => 'lastName',
        'label' => Yii::t('app', 'Last Name'),
        'value' => function (Customer $model) {
            return $model->last_name;
        },
    ],
    [
        'attribute' => 'status',
        'filter' => array_map(function ($el) {
            return Yii::t('app', $el);
        }, Customer::STATUSES),
        'value' => function (Customer $model) {
            return Yii::t('app', Customer::STATUSES[$model->status] ?? null);
        }
    ],
    [
        'attribute' => 'createdAt',
        'label' => Yii::t('app', 'Creation date'),
        'format' => 'datetime',
        'value' => function (Customer $model) {
            return $model->created_at;
        },
        'filter' => DateRangePicker::widget([
            'model' => $searchModel,
            'attribute' => 'createdAt',
            'convertFormat' => true,
            'hideInput' => false,
            'presetDropdown' => false,
            'pluginOptions' => [
                'removeButton' => [
                    'icon' => 'trash',
                ],
                'timePicker' => false,
                'timePicker24Hour' => true,
                'timePickerIncrement' => 5,
                'locale' => [
                    'format' => 'd-m-Y',
                    'autoclose' => true,
                ],
                'opens' => 'left',
            ],
            'pluginEvents' => [
                'apply.daterangepicker' => "function(ev, picker) {
                            $(picker.element).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY')).trigger('change')
                       }",
            ]
        ]),
    ],
    [
        'class' => ActionColumn::class,
        'template' => '{update} {delete}',
        'buttons' => [
            'update',
            'delete',
        ],
        'visibleButtons' => [
            'delete' => function ($model) {
                return !(bool)$model->deleted_at;
            },
            'update' => function ($model) {
                return !(bool)$model->deleted_at;
            },
        ]
    ],
];
?>
<div class="customer-index">
    <?= \common\widgets\AppGridView::widget([
        'columns' => $cols,
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
                    'label' => '<i class="fa fa-plus"></i> '.Yii::t('app', 'Add'),
                    'url' => ['create'],
                ],
                [
                    'label' => '{export}'
                ],
            ]
        ],
        'resizableColumns'=>true,
        'responsive'=>true,
        'responsiveWrap'=>true,
    ]); ?>


</div>
