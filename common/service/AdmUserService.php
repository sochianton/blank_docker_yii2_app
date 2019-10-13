<?php


namespace common\service;


use common\models\Company;
use common\widgets\AppGridView;
use kartik\daterange\DateRangePicker;
use kartik\grid\ActionColumn;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class AdmUserService
{

    /**
     * @return array
     */
    static function getCurUserMenu(): array{

        if(Yii::$app->user->isGuest) return [
            [
                'label' => '<i class="fa fa-sign-in"></i>
                    <span>'.Yii::t('app', 'Login').'</span>',
                'url' => Url::toRoute(['/site/login']),
                'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
                    if(Yii::$app->controller->id == 'site' AND Yii::$app->controller->action->id == 'login')
                        return true;
                    return false;
                },
            ],
        ];

        return [
            [
                'label' => Yii::t('app', 'Main menu'),
                'options' => [
                    'class' => 'header'
                ],
            ],
            [
                'label' => '<i class="fa fa-industry"></i>
                    <span>'.Yii::t('app', 'Companies').'</span>',
                'url' => Url::toRoute(['/company']),
                'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
                    if(Yii::$app->controller->id == 'company')
                        return true;
                    return false;
                },
            ],
            [
                'label' => '<i class="fa fa-diamond"></i>
                    <span>'.Yii::t('app', 'Customers').'</span>',
                'url' => Url::toRoute(['/customer']),
                'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
                    if(Yii::$app->controller->id == 'customer')
                        return true;
                    return false;
                },
            ],
            [
                'label' => '<i class="fa fa-life-ring"></i>
                    <span>'.Yii::t('app', 'Employees').'</span>',
                'url' => Url::toRoute(['/employee']),
                'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
                    if(Yii::$app->controller->id == 'employee')
                        return true;
                    return false;
                },
            ],
            [
                'label' => '<i class="fa fa-folder-open"></i>
                    <span>'.Yii::t('app', 'Categories').'</span>',
                'url' => Url::toRoute(['/qualification']),
                'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
                    if(Yii::$app->controller->id == 'qualification')
                        return true;
                    return false;
                },
            ],
            [
                'label' => '<i class="fa fa-gavel"></i>
                    <span>'.Yii::t('app', 'Works').'</span>',
                'url' => Url::toRoute(['/work']),
                'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
                    if(Yii::$app->controller->id == 'work')
                        return true;
                    return false;
                },
            ],
            [
                'label' => '<i class="fa fa-ticket"></i>
                    <span>'.Yii::t('app', 'Bids').'</span>',
                'url' => Url::toRoute(['/bid']),
                'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
                    if(Yii::$app->controller->id == 'bid')
                        return true;
                    return false;
                },
            ],
            [
                'label' => '<i class="fa fa-exchange"></i>
                    <span>'.Yii::t('app', 'Transactions').'</span>',
                'url' => Url::toRoute(['/transaction']),
                'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
                    if(Yii::$app->controller->id == 'transaction')
                        return true;
                    return false;
                },
            ],
        ];
    }

    /**
     * @param $dataProvider
     * @param $searchModel
     * @param array $options
     * @param null $configOnly
     * @return array|object
     * @throws \yii\base\InvalidConfigException
     */
    static function getGridWidget($dataProvider, Model $searchModel, $options=[], $configOnly=null){

        $options = ArrayHelper::merge([], $options);
        $columns = [
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
                'class' => ActionColumn::class,
                'template' => '{view} {update} {block} {restore} {delete}',
                'buttons' => [
                    'view',
                    'update',
                    'delete',
                    'block' => function (string $url, Company $model) {
                        return Html::a('<span class="fa fa-lock"></span>', ['block', 'id' => $model->id], [
                            'title' => Yii::t('app', 'Block'),
                            'class' => '',
                            'data' => [
                                'confirm' => Yii::t('app', 'You want to block this company. Are you sure?'),
                                'method' => 'post',
                            ],
                        ]);
                    },
                    'restore' => function ($url) {
                        return Html::a('<span class="fa fa-unlock"></span>', $url, [
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
        ];
        $config = [
            'class' => AppGridView::class,
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
            'columns' => $columns,
        ];

        if($configOnly){
            return $config;
        }
        else{
            return Yii::createObject($config);
        }

    }

}