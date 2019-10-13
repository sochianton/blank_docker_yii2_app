<?php


namespace common\ar;


use common\interfaces\CRUDControllerModelInterface;
use common\repositories\WorkRep;
use common\services\QualificationService;
use common\services\WorkService;
use common\widgets\AppForm;
use common\widgets\AppGridView;
use kartik\daterange\DateRangePicker;
use kartik\grid\ActionColumn;
use kartik\widgets\Select2;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;


/**
 * @property integer $id
 * @property string $name [varchar(100)]
 * @property string $price [integer]
 * @property string $commission [integer]
 */
class Work extends AppActiveRecord implements CRUDControllerModelInterface
{

    public $qualifications; // Нужно для формы поиска

    public static function tableName(): string
    {
        return '{{%work}}';
    }

    public function rules()
    {

        return ArrayHelper::merge(parent::rules(), [
            ['qualifications', function ($attribute, $params, $validator) {
                $val = $this->$attribute;
                $label = $this->getAttributeLabel($attribute);
                if(is_array($val)){
                    foreach ($val as $v){
                        if(!QualificationService::isExist($v)){
                            $this->addError($attribute, Yii::t('yii', '{attribute} is invalid.', ['attribute' => $label]));
                            return;
                        }
                    }
                }
                elseif(!QualificationService::isExist($val)){
                    $this->addError($attribute, Yii::t('yii', '{attribute} is invalid.', ['attribute' => $label]));
                    return;
                }
            }],

            [['name', 'price', 'commission'], 'required', 'except' => [
                self::SCENARIO_SEARCH
            ]],
            ['name', 'string', 'max' => 100],
            [['commission'], 'integer', 'min' => 0],
            ['commission', 'integer', 'max' => 100],
            [['price'], 'double'],
        ]);
    }

    public function attributeLabels(): array
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'price' => Yii::t('app', 'Price'),
            'commission' => Yii::t('app', 'Commission'),
            'qualifications' => Yii::t('app', 'Categories'),
        ]);
    }

    public function afterFind()
    {

        $this->qualifications = WorkService::getQualificationIds($this->id);

        parent::afterFind();



    }

    function extraFields()
    {
        return ArrayHelper::merge(parent::extraFields(), [
            'deletedAt' => 'deleted_at',
            'qualificationIds' => 'qualifications',
        ]);
    }

    // ======================================================

    public static function getService()
    {
        return WorkService::class;
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

        if (!empty($this->createdAt) && strpos($this->createdAt, ' - ') !== false) {
            [$createdAtStart, $createdAtEnd] = explode(' - ', $this->createdAt);

            $startDate = (new \DateTime($createdAtStart))->getTimestamp();
            $endDate = $createdAtStart === $createdAtEnd
                ? (new \DateTime($createdAtEnd))->modify('+ 1 day last second')->getTimestamp() // add 1 day
                : (new \DateTime($createdAtEnd))->getTimestamp();

            $query->andFilterWhere([
                'between',
                'created_at',
                $startDate,
                $endDate
            ]);
        }

        $query
            ->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['ilike', 'price', $this->price])
            ->andFilterWhere(['ilike', 'commission', $this->commission])
        ;

        return $dataProvider;

    }

    /**
     * Возвращает таблицу моедли
     * @param $dataProvider
     * @param Model $searchModel
     * @param array $params
     * @param null $configOnly
     * @return array|mixed|object
     * @throws \yii\base\InvalidConfigException
     */
    static function getGridWidget($dataProvider, Model $searchModel, $params=[], $configOnly=null){

        $options = ArrayHelper::merge([], $params);
        $columns = [
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
                    'data' => ArrayHelper::map(QualificationService::getList(), 'id', 'name'),
                    'options' => [
                        'multiple' => true,
                        'placeholder' => Yii::t('app', 'Select a category...'),
                    ],
                ]),
                'value' => function (Work $model){
                    $qualificationIds = WorkService::getQualificationIds($model->id);
                    if (empty($qualificationIds)) {
                        return null;
                    }
                    $qualifications = QualificationService::getList(false, $qualificationIds);
                    $list = array_map(function (\common\ar\Qualification $qualification) {
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
            'deleted_at:datetime',
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
        $config = [
            'class' => AppGridView::class,
            'id' => 'grid_'.$searchModel->formName(),
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'rowOptions' => function (self $model) {
                return [
                    'class' => ((bool)$model->deleted_at?'bg-danger' : ''),
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

        return Yii::$app->controller->renderPartial('@app/views/forms/work', [
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