<?php

use backend\models\search\EmployeeSearch;
use common\models\Employee;
use common\models\Qualification;
use common\service\EmployeeService;
use common\service\QualificationService;
use kartik\daterange\DateRangePicker;
use kartik\widgets\Select2;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel EmployeeSearch */
/* @var $employeeService EmployeeService */
/* @var $qualificationService QualificationService */

$this->title = Yii::t('app', 'Employees');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Employee'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
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
                'class' => yii\grid\ActionColumn::class,
                'template' => '{view} {update} {block} {restore}',
                'buttons' => [
                    'view',
                    'update',
                    'block' => function (string $url, Employee $model) {
                        return Html::a('<span class="fas fa-lock"></span>', ['block', 'id' => $model->id], [
                            'title' => Yii::t('app', 'Block'),
                            'class' => '',
                            'data' => [
                                'confirm' => Yii::t('app', 'You want to block this employee. Are you sure?'),
                                'method' => 'post',
                            ],
                        ]);
                    },
                    'restore' => function ($url) {
                        return Html::a('<span class="fas fa-lock-open"></span>', $url, [
                            'title' => Yii::t('app', 'Restore'),
                        ]);
                    },
                ],
                'visibleButtons' => [
                    'block' => function (Employee $model) {
                        return $model->status === Employee::STATUS_ACTIVE;
                    },
                    'restore' => function ($model) {
                        return $model->status === Employee::STATUS_DELETED;
                    },
                ]
            ],
        ],
    ]); ?>


</div>
