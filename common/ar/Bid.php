<?php


namespace common\ar;


use common\interfaces\CRUDControllerModelInterface;
use common\interfaces\ImportRecordInterface;
use common\services\BidService;
use common\services\WorkService;
use common\widgets\AppForm;
use common\widgets\AppGridView;
use kartik\daterange\DateRangePicker;
use kartik\grid\ActionColumn;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%bid}}".
 *
 * @property string $name [varchar(255)]
 * @property int $customer_id
 * @property int $employee_id
 * @property int $status
 * @property int $price
 * @property string $object
 * @property string $complete_at
 * @property string $customer_comment
 * @property string $employee_comment
 *
 * @property User $customer_rl
 */
class Bid extends AppActiveRecord implements CRUDControllerModelInterface, ImportRecordInterface
{

    /** @var array */
    public $works; //форма
    public $categoryName='';

    public $customerPhotos; //форма
    public $customerPhotosArr;
    public $employeePhotos; //форма
    public $employeePhotosArr;
    public $files; //форма
    public $filesArr;

    const EVENT_CREATE_BID_BY_CUSTOMER = 'bid_create_customer';
    const EVENT_APPLY_BID_BY_EMPLOYEE = 'bid_apply_employee';
    const EVENT_REJECT_BID_BY_EMPLOYEE = 'bid_reject_employee';
    const EVENT_DONE_BID_BY_EMPLOYEE = 'bid_done_employee';
    const EVENT_BID_CANCELED = 'bid_canceled';

    const EVENT_CREATE_UPDATE_BID = 'bid_create_update';



    const STATUS_NEW = 10;
    const STATUS_CANCELED = 20;
    const STATUS_IN_WORK = 30;
    const STATUS_OUTDATED = 40;
    const STATUS_CONFIRMATION = 50;
    const STATUS_COMPLETE = 60;
    const STATUS_ARBITRATION = 70;

    const STATUSES = [
        self::STATUS_NEW => 'New',
        self::STATUS_CANCELED => 'Canceled',
        self::STATUS_IN_WORK => 'In Work',
        self::STATUS_OUTDATED => 'Outdated',
        self::STATUS_CONFIRMATION => 'Confirmation',
        self::STATUS_COMPLETE => 'Complete',
        self::STATUS_ARBITRATION => 'Arbitration',
    ];

    const STATUSES_ACTIVE = [
        self::STATUS_NEW,
        self::STATUS_IN_WORK,
        self::STATUS_CONFIRMATION,
        self::STATUS_ARBITRATION,
    ];

    const STATUSES_ARCHIVE = [
        self::STATUS_CANCELED,
        self::STATUS_OUTDATED,
        self::STATUS_COMPLETE,
    ];

    public static function tableName()
    {
        return '{{%bid}}';
    }

    public function rules()
    {

        return ArrayHelper::merge(parent::rules(), [
            [['name', 'customer_id', 'status', 'complete_at', 'price', 'object', 'works'], 'required', 'except' => [
                self::SCENARIO_SEARCH
            ]],

            [
                ['customer_id'],
                'exist',
                'targetClass' => User::class,
                'filter' => ['type'=>User::TYPE_CUSTOMER],
                'targetAttribute' => 'id'
            ],
            [
                ['employee_id'],
                'exist',
                'targetClass' => User::class,
                'filter' => ['type'=>User::TYPE_EMPLOYEE],
                'targetAttribute' => 'id'
            ],
            [['price'], 'integer', 'min' => 1, 'max' => 2147483647],
            [['price'], 'default', 'value' => 0, 'except' => [
                self::SCENARIO_SEARCH
            ]],
            [['name', 'object'], 'string', 'max' => 100],

            [['complete_at'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],


            ['status', 'in', 'range' => array_keys(self::STATUSES),
                'when' => function ($model, $attribute) {
                    return (bool)(!is_array($model->$attribute));
                }
            ],
            [
                'status',
                'each',
                'rule' => [
                    'in', 'range' => array_keys(self::STATUSES),
                ],
                'when' => function ($model, $attribute) {
                    return (bool)(is_array($model->$attribute));
                }
            ],
            ['works', 'each', 'rule' => ['integer']],
            ['works', 'each', 'rule' => [
                    'exist',
                    'targetClass' => Work::class,
                    'targetAttribute' => 'id'
                ]
            ],

            [
                ['customerPhotos'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'png, jpg, jpeg',
                'maxFiles' => BidAttachment::MAX_PHOTOS_CUSTOMER,
                'when' => function ($model, $attribute) {
                    return $model->$attribute instanceof UploadedFile;
                }
            ],
            [
                ['employeePhotos'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'png, jpg, jpeg',
                'maxFiles' => BidAttachment::MAX_PHOTOS_EMPLOYEE,
                'when' => function ($model, $attribute) {
                    return $model->$attribute instanceof UploadedFile;
                }
            ],
            [
                ['files'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'pdf, doc, docx, xls, xlsx, xlsm',
                'maxFiles' => BidAttachment::MAX_FILES,
                'when' => function ($model, $attribute) {
                    return $model->$attribute instanceof UploadedFile;
                }
            ],

            [['customer_comment', 'employee_comment'], 'string', 'max' => 500],
        ]);
    }

    public function attributeLabels(): array
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'customer_id' => Yii::t('app', 'Customer ID'),
            'employee_id' => Yii::t('app', 'Employee ID'),
            'status' => Yii::t('app', 'Status'),
            'price' => Yii::t('app', 'Price'),
            'object' => Yii::t('app', 'Object'),
            'customer_comment' => Yii::t('app', 'Customer Comment'),
            'employee_comment' => Yii::t('app', 'Employee Comment'),
            'complete_at' => Yii::t('app', 'Complete At'),

            'works' => Yii::t('app', 'Works'),
            'customerPhotos' => Yii::t('app', 'Customer Photos'),
            'employeePhotos' => Yii::t('app', 'Employee Photos'),
            'files' => Yii::t('app', 'Files'),
        ]);
    }

    /**
     * @param array $data
     * @param null $formName
     * @return bool
     */
    public function load($data, $formName = null): bool
    {
        $load = parent::load($data, $formName);
        $this->customerPhotos = UploadedFile::getInstances($this, 'customerPhotos');
        $this->employeePhotos = UploadedFile::getInstances($this, 'employeePhotos');
        $this->files = UploadedFile::getInstances($this, 'files');
        return $load;
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function afterFind()
    {

        $this->filesArr = BidService::getFiles($this->id, BidAttachment::TYPE_FILE);
        $this->customerPhotosArr = BidService::getFiles($this->id, BidAttachment::TYPE_PHOTO_CUSTOMER);
        $this->employeePhotosArr = BidService::getFiles($this->id, BidAttachment::TYPE_PHOTO_EMPLOYEE);
        $this->works = ArrayHelper::getColumn($this->works_rl, 'id');


        parent::afterFind();
    }

    function extraFields()
    {
        return ArrayHelper::merge(parent::extraFields(), [
            'customerId' => 'customer_id',
            'employeeId' => 'employee_id',
            'completeAt' => 'complete_at',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at',
            'customerComment' => 'customer_comment',
            'categoryName' => 'categoryName',
            'employeeComment' => 'employee_comment',
            'works' => 'works_rl',
            'customerPhotos' => 'customerPhotosArr',
            'employeePhotos' => 'employeePhotosArr',
            'files' => 'filesArr',
        ]);
    }

    // ======== RELATIONS ==================================

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getWorks_rl()
    {

        return $this->hasMany(Work::class, ['id' => 'work_id'])
            ->viaTable(BidWork::tableName(), ['bid_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getFiles_rl()
    {

        return $this->hasMany(BidAttachment::class, ['bid_id' => 'id'])->andWhere(['type' => BidAttachment::TYPE_FILE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getCustomerphotoes_rl()
    {

        return $this->hasMany(BidAttachment::class, ['bid_id' => 'id'])->andWhere(['type' => BidAttachment::TYPE_PHOTO_CUSTOMER]);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getEmployeephotoes_rl()
    {

        return $this->hasMany(BidAttachment::class, ['bid_id' => 'id'])->andWhere(['type' => BidAttachment::TYPE_PHOTO_EMPLOYEE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getCustomer_rl()
    {

        return $this->hasOne(User::class, ['id' => 'customer_id'])->andWhere(['type' => User::TYPE_CUSTOMER]);
    }

    // ======================================================

    /**
     * @return mixed|BidService
     */
    public static function getService()
    {
        return BidService::class;
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
        $completeAtStart = null;
        $completeAtEnd = null;
        $this->load($params);

        if (!$this->validate()) {
            $query->andWhere('1=0');
            throw new Exception('Errors in search params', $this->getErrors());
            //return $dataProvider;
        }

//        die(print_r($this->attributes, true));

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

        if (!empty($this->complete_at) && strpos($this->complete_at, ' - ') !== false) {
            [$completeAtStart, $completeAtEnd] = explode(' - ', $this->complete_at);

            $startDate = (new \DateTime($completeAtStart))->format(DATE_ATOM);
            $endDate = $completeAtStart === $completeAtEnd
                ? (new \DateTime($completeAtEnd))->modify('+ 1 day last second')->format(DATE_ATOM) // add 1 day
                : (new \DateTime($completeAtEnd))->format(DATE_ATOM);

            $query->andFilterWhere([
                'between',
                'complete_at',
                $startDate,
                $endDate
            ]);
        }

        $query
            ->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'object', $this->object])
            ->andFilterWhere(['ilike', 'price', $this->price])
            ->andFilterWhere(['status' => $this->status])
            ->andFilterWhere(['employee_id' => $this->employee_id])
            ->andFilterWhere(['customer_id' => $this->customer_id])
            ->andFilterWhere(['id' => $this->id])
        ;

        if(isset($params['employee_id']) AND $params['employee_id'] === false){
            $query->andWhere(['employee_id' => null]);
        }

//        die(print_r($params, true));
//        die(print_r($this->attributes, true));



        return $dataProvider;

    }

    /**
     * Возвращает таблицу моедли
     * @param $dataProvider
     * @param Model $searchModel
     * @param array $params
     * @param null $configOnly
     * @return AppGridView
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
            'customer_id',
            'employee_id',
            [
                'attribute' => 'status',
                'filter' => array_map(function ($el) {
                    return Yii::t('app', $el);
                }, Bid::STATUSES),
                'value' => function ($model) {
                    return Yii::t('app', Bid::STATUSES[$model->status] ?? null);
                }
            ],
            'price:currency',
            'object',
            [
                'attribute' => 'completeAt',
                'label' => Yii::t('app', 'Completion date'),
                'format' => 'date',
                'value' => function ($model) {
                    return $model->complete_at;
                },
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'complete_at',
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
                'attribute' => 'createdAt',
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
                        return false;//!(bool)$model->deleted_at;
                    },
                    'update' => function ($model) {
                        return !(bool)$model->deleted_at AND Yii::$app->user->ch('/bid/update');
                    },
                ]
            ],
        ];
        $config = [
            'class' => AppGridView::class,
            'id' => 'grid_'.$searchModel->formName(),
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'rowOptions' => function ($model) {
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
                        'visible' => Yii::$app->user->ch('/bid/create')
                    ],
                    [
                        'label' => '<i class="glyphicon glyphicon-cloud-upload"></i> '.Yii::t('app', 'Upload'),
                        'url' => ['upload'],
                        'visible' => Yii::$app->user->ch('/bid/upload')
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
            'type' => AppForm::TYPE_VERTICAL,
            'box' => [],
        ];

        return Yii::$app->controller->renderPartial('@app/views/forms/bid', [
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

    // ===========Import==========================

    /**
     * @return array|null
     */
    static function importHeader(){

        return array (
            'name',
            'customer_id',
            'employee_id',
            'status',
            'price',
            'object',
            'works',
            'customer_comment',
            'employee_comment',
            'complete_at',
        );

    }


    /**
     * @return array
     */
    static function importHeaderDescription(){

        return array (
            'complete_at' => 'Формат даты: 2019-10-25 18:00:00',

            'status' => implode(",\n", array_map(function ($el) {
                return Yii::t('app', $el);
            }, Bid::STATUSES)),

            'works' => implode(",\n", WorkService::getNames()),

        );

    }


    /**
     * @return array
     */
    static function importAttributeRules(){

        return array (

            'status' => function($val){



                $src = array_map(function ($el) {
                    return Yii::t('app', $el);
                }, Bid::STATUSES);

                $src = array_flip($src);

                //die('<pre>'.print_r($src, true).'</pre>');

                if(isset($src[$val])) return (int)$src[$val];
                else return $val;
            },
            'works' => function($val){



                $vals = explode(',', $val);



                $vals = array_map(function ($el) {
                    return trim($el);
                }, $vals);

//                die('<pre>'.print_r(WorkService::getIdsByName($vals), true).'</pre>');

                return WorkService::getIdsByName($vals);

            }

        );

    }

}





