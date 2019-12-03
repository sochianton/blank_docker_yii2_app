<?php


namespace common\ar;


use common\interfaces\CRUDControllerModelInterface;
use common\services\TransactionService;
use common\widgets\AppForm;
use common\widgets\AppGridView;
use kartik\daterange\DateRangePicker;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\helpers\ArrayHelper;


/**
 * @property integer    $id
 * @property float      $amount
 * @property integer    $from
 * @property integer    $to
 * @property string     $comment
 *
 *
 *
 * @property User     $to_rl
 * @property User     $from_rl
 */
class Transactions extends AppActiveRecord implements CRUDControllerModelInterface
{

    public static function tableName(): string
    {
        return '{{%transactions}}';
    }

    public function rules()
    {

        return ArrayHelper::merge(parent::rules(), [

            [['amount'], 'required', 'except' => [
                self::SCENARIO_SEARCH
            ]],
            [['to'], 'required', 'on' => [
                self::SCENARIO_CREATE
            ]],


            ['comment', 'string', 'max' => 255],
            [['amount'], 'double', 'except' => [
                self::SCENARIO_SEARCH
            ]],

            [['amount',], 'filter', 'filter' => function($val){ return trim($val);}],
            [['amount',], 'string', 'on' => [
                self::SCENARIO_SEARCH
            ]],

            [['from', 'to'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id']

        ]);
    }

    public function attributeLabels(): array
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'amount' => Yii::t('app', 'Amount'),
            'from' => Yii::t('app', 'User from'),
            'to' => Yii::t('app', 'User to'),
            'comment' => Yii::t('app', 'Comment'),
        ]);
    }

    //=============== RELATIONS ============================================================

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getTo_rl()
    {
        return $this->hasOne(User::class, ['id' => 'to']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getFrom_rl()
    {
        return $this->hasOne(User::class, ['id' => 'from']);
    }

    // ======================================================

    public static function getService()
    {
        return TransactionService::class;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     * @throws Exception
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
            throw new Exception('Errors in search params', $this->getErrors());
        }

        if (!empty($this->created_at) && strpos($this->created_at, ' - ') !== false) {
            [$createdAtStart, $createdAtEnd] = explode(' - ', $this->created_at);

            $startDate = (new \DateTime($createdAtStart))->format(DATE_ATOM);
            $endDate = $createdAtStart === $createdAtEnd
                ? (new \DateTime($createdAtEnd))->modify('+ 1 day last second')->format(DATE_ATOM) // add 1 day
                : (new \DateTime($createdAtEnd))->format(DATE_ATOM);

            $query->andFilterWhere([
                'between',
                'created_at',
                $startDate,
                $endDate
            ]);
        }

        // Баланс
        if(1){
            $amountVal=null;
            $operator = '=';
            if($this->amount){

                if(stristr($this->amount, ' ')){
                    $parts = explode(' ', $this->amount);
                    if(isset($parts[0])) $operator = $parts[0];
                    if(isset($parts[1])) $amountVal = (float)$parts[1];
                    if(!in_array($operator, [
                        '=',
                        '>',
                        '<',
                        '=<',
                        '=>',
                        '<=',
                        '>=',
                        '<>',
                    ])){
                        $operator = '=';
                    }
                }
                else{
                    $amountVal = (float)$this->amount;
                }

            }
            $query
                ->filterWhere([$operator, 'amount', $amountVal]);
        }


        $query
            ->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['from' => $this->from])
            ->andFilterWhere(['to' => $this->to])
            ->andFilterWhere(['ilike', 'comment', $this->comment])
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

        $columns = [
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width: 30px'],
                'filter' => false
            ],
            [
                'attribute' => 'to_rl.fullName',
                'header' => $searchModel->getAttributeLabel('to'),
            ],
            [
                'attribute' => 'from_rl.fullName',
                'header' => $searchModel->getAttributeLabel('from'),
            ],
            'amount:currency',
            'comment:text',
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
                        'label' => '<i class="fa fa-plus"></i> '.Yii::t('app', 'Add manually'),
                        'url' => ['create'],
                        'visible' => Yii::$app->user->ch('/work/create')
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
     * @throws \yii\base\InvalidConfigException
     */
    function getForm($params=null):string{

        $formConfig = [
            'id' => $this->formName(),
            'type' => AppForm::TYPE_HORIZONTAL,
            'box' => [],
        ];

        return Yii::$app->controller->renderPartial('@app/views/forms/transaction', [
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

    return <<<JS

jQuery('#transactions-to').select2({
    width: '100%',
    minimumInputLength: 2,
    ajax: {
        url: '/user/ajax-search',
        dataType: 'json',
        type: 'POST',
        data: function (params) {
            
            return {
                params:{
                    searchTerm: params.term
                }
            };
          
        },
        processResults: function (data) {
          // Transforms the top-level key of the response object from 'items' to 'results'
          return {
            results: data
          };
        }
    }
});

JS;


    }

}