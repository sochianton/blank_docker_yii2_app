<?php

use kartik\daterange\DateRangePicker;
use kartik\icons\Icon;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\Qualification */
/* @var $searchModel backend\models\search\QualificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Categories');
$this->params['breadcrumbs'][] = $this->title;
Icon::map($this, Icon::FAS);
?>
<div class="qualification-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Category'), ['create'], ['class' => 'btn btn-success']) ?>
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

            [
                'attribute' => 'name',
            ],

            [
                'attribute' => 'createdAt',
                'label' => Yii::t('app', 'Creation date'),
                'format' => 'date',
                'value' => function ($model) {
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
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {block} {restore}',
                'buttons' => [
                    'view',
                    'update',
                    'block' => function ($url, $model) {
                        return Html::a('<span class="fas fa-lock"></span>', ['block', 'id' => $model->id], [
                            'title' => Yii::t('app', 'Block'),
                            'class' => '',
                            'data' => [
                                'confirm' => Yii::t('app', 'You want to block this qualification. Are you sure?'),
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
