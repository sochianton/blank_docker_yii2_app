<?php


namespace common\ar;


use common\interfaces\CRUDControllerModelInterface;
use common\services\QualificationService;
use common\widgets\AppForm;
use common\widgets\AppGridView;
use kartik\daterange\DateRangePicker;
use kartik\grid\ActionColumn;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;


/**
 * Qualification model
 *
 * @property string $name
 */
class Qualification extends AppActiveRecord implements CRUDControllerModelInterface
{

    public static function tableName(): string
    {
        return '{{%qualification}}';
    }

    public function rules()
    {

        return ArrayHelper::merge(parent::rules(), [
            ['name', 'required', 'except' => [
                self::SCENARIO_SEARCH
            ]],
            ['name', 'string', 'max' => 100],
        ]);
    }

    // ========================================================================================================================

    public static function getService()
    {
        return QualificationService::class;
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
            ->andFilterWhere(['=', 'id', $this->id])
        ;

        return $dataProvider;

    }

    /**
     * Возвращает таблицу моедли
     * @param $dataProvider
     * @param Model $searchModel
     * @param array $params
     * @param null $configOnly
     * @return mixed
     */
    static function getGridWidget($dataProvider, Model $searchModel, $params=[], $configOnly=null){

        $options = ArrayHelper::merge([], $params);
        $columns = [
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width: 30px']
            ],

            [
                'attribute' => 'name',
            ],

            [
                'attribute' => 'created_at',
                'label' => Yii::t('app', 'Creation date'),
                'format' => 'date',
                'value' => function ($model) {
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
            'deleted_at:datetime',
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
                'visibleButtons' => [
                    'delete' => function (self $model) {
                        return $model->deleted_at == null;
                    },
                    'update' => function (self $model) {
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
            'rowOptions' => function (self $model) {
                return [
                    'class' => ($model->deleted_at!=null?'bg-danger' : ''),
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

    /**
     * Получаем основную форму модели
     * @param null $params
     * @return string
     */
    function getForm($params=null):string{

        $formConfig = [
            'id' => $this->formName(),
            'type' => AppForm::TYPE_HORIZONTAL,
            'box' => [],
        ];

        return Yii::$app->controller->renderPartial('@app/views/forms/qualification', [
            'model' => $this,
            'formConfig' => $formConfig,
        ]);

    }

    /**
     * Скрипты, которые вызываются при отображении Формы
     * @param $params
     * @return string
     */
    public function formScripts($params=null): string {



    }



}