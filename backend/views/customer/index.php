<?php

use backend\models\search\CustomerSearch;
use common\models\Customer;
use kartik\daterange\DateRangePicker;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel CustomerSearch */

$this->title = Yii::t('app', 'Customers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Customer'), ['create'], ['class' => 'btn btn-success']) ?>
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
                'class' => yii\grid\ActionColumn::class,
                'template' => '{view} {update} {block} {restore}',
                'buttons' => [
                    'view',
                    'update',
                    'block' => function (string $url, Customer $model) {
                        return Html::a('<span class="fas fa-lock"></span>', ['block', 'id' => $model->id], [
                            'title' => Yii::t('app', 'Block'),
                            'class' => '',
                            'data' => [
                                'confirm' => Yii::t('app', 'You want to block this customer. Are you sure?'),
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
                    'block' => function (Customer $model) {
                        return $model->status === Customer::STATUS_ACTIVE;
                    },
                    'restore' => function ($model) {
                        return $model->status === Customer::STATUS_DELETED;
                    },
                ]
            ],
        ],
    ]); ?>


</div>
