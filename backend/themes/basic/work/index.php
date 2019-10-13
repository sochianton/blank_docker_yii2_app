<?php

use common\models\Employee;
use common\models\Qualification;
use common\models\Work;
use common\service\QualificationService;
use common\service\WorkService;
use kartik\daterange\DateRangePicker;
use kartik\icons\Icon;
use kartik\widgets\Select2;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model Work */
/* @var $searchModel backend\models\search\WorkSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $workService WorkService */
/* @var $qualificationService QualificationService */

$this->title = Yii::t('app', 'Works');
$this->params['breadcrumbs'][] = $this->title;
Icon::map($this, Icon::FAS);
?>
<div class="work-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Work'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model) {
            return [
                'style' => 'background-color: ' . (!empty($model->deleted_at) ? '#b36c6c' : '')
            ];
        },
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width: 30px']
            ],
            'name',
            'price:currency',
            [
                'label' => Yii::t('app', 'Categories'),
                'format' => 'raw',
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'qualifications',
                    'data' => ArrayHelper::map($qualificationService->getList(), 'id', 'name'),
                    'options' => [
                        'multiple' => true,
                        'placeholder' => Yii::t('app', 'Select a category...'),
                    ],
                ]),
                'value' => function (Work $model) use ($workService, $qualificationService) {
                    $qualificationIds = $workService->getQualificationIds($model->id);
                    if (empty($qualificationIds)) {
                        return null;
                    }
                    $qualifications = $qualificationService->getList(false, $qualificationIds);
                    $list = array_map(function (Qualification $qualification) {
                        return $qualification->name;
                    }, $qualifications);
                    sort($list);
                    return implode(', ', $list);
                }
            ],
            [
                'attribute' => 'commission',
                'value' => function ($model) {
                    return $model->commission . '%';
                },
            ],
            [
                'attribute' => 'createdAt',
                'label' => Yii::t('app', 'Creation date'),
                'format' => 'date',
                'value' => function (Work $model) {
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
                    'block' => function (string $url, Work $model) {
                        return Html::a('<span class="fas fa-lock"></span>', ['block', 'id' => $model->id], [
                            'title' => Yii::t('app', 'Block'),
                            'class' => '',
                            'data' => [
                                'confirm' => Yii::t('app', 'You want to block this work. Are you sure?'),
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
                    'block' => function ($model) {
                        return !(bool)$model->deleted_at;
                    },
                    'restore' => function ($model) {
                        return (bool)$model->deleted_at;
                    },
                ]
            ],
        ],
    ]); ?>
</div>
