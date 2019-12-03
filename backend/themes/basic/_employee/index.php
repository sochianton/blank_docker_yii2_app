<?php

use backend\models\search\EmployeeSearch;
use common\models\Employee;
use common\models\Qualification;
use common\service\EmployeeService;
use common\service\QualificationService;
use common\widgets\AppGridView;
use kartik\daterange\DateRangePicker;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel EmployeeSearch */
/* @var $employeeService EmployeeService */
/* @var $qualificationService QualificationService */

$this->title = Yii::t('app', 'Employees');
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
        'value' => function (Employee $model) {
            return $model->first_name;
        },
    ],
    [
        'attribute' => 'secondName',
        'label' => Yii::t('app', 'Second Name'),
        'value' => function (Employee $model) {
            return $model->second_name;
        },
    ],
    [
        'attribute' => 'lastName',
        'label' => Yii::t('app', 'Last Name'),
        'value' => function (Employee $model) {
            return $model->last_name;
        },
    ],
    [
        'attribute' => 'status',
        'filter' => array_map(function ($el) {
            return Yii::t('app', $el);
        }, Employee::STATUSES),
        'value' => function (Employee $model) {
            return Yii::t('app', Employee::STATUSES[$model->status] ?? null);
        }
    ],
    [
        'label' => Yii::t('app', 'Qualifications'),
        'format' => 'raw',
        'filter' => Select2::widget([
            'model' => $searchModel,
            'attribute' => 'qualifications',
            'data' => ArrayHelper::map($qualificationService->getList(), 'id', 'name'),
            'options' => [
                'multiple' => true,
                'placeholder' => Yii::t('app', 'Select a qualifications ...'),
            ],
        ]),
        'value' => function (Employee $model) use ($employeeService, $qualificationService) {
            $employeeQualificationIds = $employeeService->getQualificationIds($model->id);
            if (empty($employeeQualificationIds)) {
                return null;
            }
            $qualifications = $qualificationService->getList(false, $employeeQualificationIds);
            $list = array_map(function (Qualification $qualification) {
                return $qualification->name;
            }, $qualifications);
            sort($list);
            return implode(', ', $list);
        }
    ],
    [
        'attribute' => 'balance',
        'value' => function (Employee $model) {
            return $model->balance;
        }
    ],
    [
        'attribute' => 'createdAt',
        'label' => Yii::t('app', 'Creation date'),
        'format' => 'datetime',
        'value' => function (Employee $model) {
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
        'class' => \kartik\grid\ActionColumn::class,
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
<div class="employee-index">
    <?= AppGridView::widget([
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
        'columns' => $cols,
    ]); ?>


</div>
