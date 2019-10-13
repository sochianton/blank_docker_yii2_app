<?php


namespace common\ar;


use common\interfaces\CRUDControllerModelInterface;
use common\repositories\CompanyRep;
use common\services\CompanyService;
use common\widgets\AppForm;
use common\widgets\AppGridView;
use kartik\daterange\DateRangePicker;
use kartik\grid\ActionColumn;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%company}}".
 *
 * @property int $type
 * @property int $status
 * @property string $name
 * @property string $address
 * @property string $number_of_contract [varchar(255)]
 */
class Company extends AppActiveRecord implements CRUDControllerModelInterface
{

    const STATUS_ACTIVE = 20;
    const STATUS_BLOCKED = 30;

    const STATUSES = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_BLOCKED => 'Blocked',
    ];

    const TYPE_CLIENT = 10;
    const TYPE_CONTRACTOR = 20;

    const TYPES = [
        self::TYPE_CLIENT => 'Client',
        self::TYPE_CONTRACTOR => 'Contractor',
    ];

    public static function tableName()
    {
        return '{{%company}}';
    }

    public function rules()
    {

        return ArrayHelper::merge(parent::rules(), [
            [['type', 'status', 'name', 'address'], 'required', 'except' =>[
                self::SCENARIO_SEARCH
            ]],
            [['type', 'status'], 'default', 'value' => null],
            [['type', 'status', 'number_of_contract'], 'integer'],
            ['status', 'in', 'range' => array_keys(self::STATUSES)],
            ['type', 'in', 'range' => array_keys(self::TYPES)],
            [['name', 'address'], 'string', 'max' => 100],
        ]);
    }

    public function attributeLabels():array
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'type' => Yii::t('app', 'Type'),
            'status' => Yii::t('app', 'Status'),
            'address' => Yii::t('app', 'Address'),
            'number_of_contract' => Yii::t('app', 'Number Of Contract'),
        ]);
    }

    // ========================================================================================================================

    public static function getService()
    {
        return CompanyService::class;
    }

    /**
     * Поиск по таблице
     * @param $params
     * @return ActiveDataProvider
     * @throws \Exception
     */
    public function search($params){

        $this->setScenario(self::SCENARIO_SEARCH);

        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC
                ]
            ]
        ]);

        $createdAtStart = null;
        $createdAtEnd = null;

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!empty($this->created_at) && strpos($this->created_at, ' - ') !== false) {
            [$createdAtStart, $createdAtEnd] = explode(' - ', $this->created_at);

            $startDate = (new \DateTime($createdAtStart))->format('Y-m-d H:i:s');
            $endDate = $createdAtStart === $createdAtEnd
                ? (new \DateTime($createdAtEnd))->modify('+ 1 day last second')->format('Y-m-d H:i:s') // add 1 day
                : (new \DateTime($createdAtEnd))->format('Y-m-d H:i:s');

            $query->andFilterWhere([
                'between',
                'created_at',
                $startDate,
                $endDate
            ]);
        }

        $query
            ->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'address', $this->address])
            ->andFilterWhere(['=', 'number_of_contract', $this->number_of_contract])
            ->andFilterWhere(['=', 'type', $this->type])
            ->andFilterWhere(['=', 'status', $this->status])
            ->andFilterWhere(['=', 'id', $this->id])
        ;

        return $dataProvider;

    }

    /**
     * Возвращает таблицу
     * @param $dataProvider
     * @param Model $searchModel
     * @param array $options
     * @param null $configOnly
     * @return array|AppGridView|object
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
                }, \common\models\Company::TYPES),
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
                'attribute' => 'created_at',
                'label' => Yii::t('app', 'Creation date'),
                'format' => 'datetime',
                'value' => function (Company $model) {
                    return $model->created_at;
                },
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at',
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
                'attribute' => 'updated_at',
                'filter' => false,
            ],
            [
                'attribute' => 'deleted_at',
                'filter' => false,
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update} {block} {restore} {delete}',
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
                    'block' => function ($model) {
                        return ($model->deleted_at == null AND $model->status === Company::STATUS_ACTIVE);
                    },
                    'restore' => function ($model) {
                        return ($model->deleted_at == null AND $model->status === Company::STATUS_BLOCKED);
                    },
                    'delete' => function ($model) {
                        return $model->deleted_at == null;
                    },
                    'update' => function ( $model) {
                        return $model->deleted_at == null;
                    },
                ]
            ],
        ];
        $config = [
            'class' => AppGridView::class,
            'id' => 'grid_'.$searchModel->formName(),
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'rowOptions' => function (Company $model) {

                //die('<pre>'.print_r($model, true).'</pre>');

                return [
                    'class' => ($model->deleted_at?'bg-danger' : ''),
                ];
            },
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

    function getForm($params = null): string
    {
        $formConfig = [
            'id' => $this->formName(),
            'type' => AppForm::TYPE_HORIZONTAL,
            'box' => [],
        ];

        return Yii::$app->controller->renderPartial('@app/views/forms/company', [
            'model' => $this,
            'formConfig' => $formConfig,
        ]);
    }

    public function formScripts($params = null): string
    {

    }

}