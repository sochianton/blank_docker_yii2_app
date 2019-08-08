<?php

use backend\models\search\CompanySearch;
use common\models\Company;
use kartik\daterange\DateRangePicker;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel CompanySearch */

$this->title = Yii::t('app', 'Companies');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Company'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width: 30px']
            ],
            'name',
            [
                'attribute' => 'type',
                'filter' => array_map(function ($el) {
                    return Yii::t('app', $el);
                }, Company::TYPES),
                'value' => function (Company $model) {
                    return Yii::t('app', Company::TYPES[$model->type] ?? null);
                }
            ],
            [
                'attribute' => 'status',
                'filter' => array_map(function ($el) {
                    return Yii::t('app', $el);
                }, Company::STATUSES),
                'value' => function (Company $model) {
                    return Yii::t('app', Company::STATUSES[$model->status] ?? null);
                }
            ],
            'address',
            [
                'attribute' => 'numberOfContract',
                'label' => Yii::t('app', 'Number Of Contract'),
                'value' => function (Company $model) {
                    return $model->number_of_contract;
                }
            ],
            [
                'attribute' => 'createdAt',
                'label' => Yii::t('app', 'Creation date'),
                'format' => 'datetime',
                'value' => function (Company $model) {
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
                    'block' => function (string $url, Company $model) {
                        return Html::a('<span class="fas fa-lock"></span>', ['block', 'id' => $model->id], [
                            'title' => Yii::t('app', 'Block'),
                            'class' => '',
                            'data' => [
                                'confirm' => Yii::t('app', 'You want to block this company. Are you sure?'),
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
                    'block' => function (Company $model) {
                        return $model->status === Company::STATUS_ACTIVE;
                    },
                    'restore' => function (Company $model) {
                        return $model->status === Company::STATUS_BLOCKED;
                    },
                ]
            ],
        ],
    ]); ?>


</div>
