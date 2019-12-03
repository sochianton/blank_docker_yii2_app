<?php


namespace common\ar;

use common\interfaces\CRUDControllerModelInterface;
use common\models\BidAttachment;
use common\repositories\AuthTokenRep;
use common\repositories\UserRep;
use common\services\TransactionService;
use common\services\UserService;
use common\widgets\AppForm;
use common\widgets\AppGridView;
use kartik\daterange\DateRangePicker;
use kartik\grid\ActionColumn;
use scl\tools\rest\exceptions\SafeException;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;

/**
 * Admin model
 *
 *
 * @property string $email
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $first_name
 * @property string $second_name
 * @property string $last_name
 * @property int $status
 * @property int $type
 * @property float $balance
 * @property string $auth_key
 * @property string $phone
 * @property string $photo
 *
 * @property AuthItems[] $roles_rl
 */
class User extends AppActiveRecord implements IdentityInterface, CRUDControllerModelInterface
{

    const PATH_IMAGES = '/images/';

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const STATUSES = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_DELETED => 'Blocked',
    ];


    const TYPE_USER = 0;
    const TYPE_CUSTOMER = 10;
    const TYPE_EMPLOYEE = 30;
    const TYPES = [
        self::TYPE_USER => 'User',
        self::TYPE_CUSTOMER => 'Customer',
        self::TYPE_EMPLOYEE => 'Employee',
    ];

    public $password;
    public $passwordRepeat;
    public $qualifications=[]; // устаревший параметр (потому что используются только работы) он раньше был в форме. сейчас в форме нет
    public $works=[]; // массив с ID работ - нужен для формы
    //public $qualificationsAndWorks = []; // Для DTO работы с категориями
    /** @var UploadedFile */
    public $formPhoto;
    public $formRoles;

    public $balance; // Для отображения баланса пользователя при поиске
    public $searchTerm; // Строка для поиска

    public static function tableName(): string
    {
        return '{{%admin}}';
    }

    public function rules(): array
    {
        return ArrayHelper::merge(parent::rules(), [
            ['password_hash', 'filter', 'filter' => function ($val) {
                if ($this->password AND trim($this->password) !=='') {
                    return Yii::$app->security->generatePasswordHash($this->password);
                }
                else return $this->password_hash;
            }],
            [
                [
                    'email',
                    'first_name',
                    'second_name',
                    'last_name',
                    'status',
                    'company_id',
                ],
                'required',
                'except' => [
                    self::SCENARIO_SEARCH
                ]
            ],
            [
                'qualifications',
                'each',
                'rule' => [
                    'exist',
                    'targetClass' => Qualification::class,
                    'targetAttribute' => 'id'
                ]
            ],
            [
                'works',
                'each',
                'rule' => [
                    'exist',
                    'targetClass' => Work::class,
                    'targetAttribute' => 'id'
                ]
            ],

            ['formRoles', 'default', 'value' => []],
            ['formRoles', 'each', 'rule' => [
                'exist',
                'targetAttribute' => 'name',
                'targetClass' => AuthItems::class,
            ]],


            [['balance', 'searchTerm'], 'filter', 'filter' => function($val){ return trim($val);}],
            [['balance', 'searchTerm'], 'string', 'on' => [
                self::SCENARIO_SEARCH
            ]],

            [['status', 'company_id', 'type'], 'integer'],

            ['status', 'default', 'value' => self::STATUS_ACTIVE, 'except' => [
                self::SCENARIO_SEARCH
            ]],
            ['status', 'in', 'range' => array_keys(self::STATUSES)],
            ['type', 'in', 'range' => array_keys(self::TYPES)],
            ['company_id', 'exist', 'targetClass' => Company::class, 'targetAttribute' => 'id'],
            [
                [
                    'password_hash',
                    'password_reset_token',
                    'email',
                    'first_name',
                    'second_name',
                    'auth_key',
                    'last_name',
                    'photo',
                ],
                'string',
                'max' => 255
            ],

            [
                'formPhoto',
                'image',
                'maxSize' => BidAttachment::MAX_PHOTO_SIZE_BYTES,
                'tooBig' => Yii::t('app', 'File size limit is 2MB')
            ],

            [['phone'], 'string', 'max' => 20],

            [['email', 'phone', 'password_reset_token'], 'unique'],

            [['password', 'passwordRepeat', 'password_hash'], 'required', 'on' => [
                self::SCENARIO_CREATE
            ]],
            [['password', 'passwordRepeat'], 'string', 'min' => 6],
            [
                'passwordRepeat',
                'compare',
                'skipOnEmpty' => false,
                'compareAttribute' => 'password',
                'message' => Yii::t('app', 'Passwords don\'t match')
            ],

        ]);
    }

    public function attributeLabels(): array
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'email' => Yii::t('app', 'Email'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'first_name' => Yii::t('app', 'First Name'),
            'second_name' => Yii::t('app', 'Second Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'status' => Yii::t('app', 'Status'),
            'company_id' => Yii::t('app', 'Company'),
            'type' => Yii::t('app', 'Type'),
            'phone' => Yii::t('app', 'Phone'),
            'password' => Yii::t('app', 'Password'),
            'passwordRepeat' => Yii::t('app', 'Repeat password'),
            'qualifications' => Yii::t('app', 'Qualifications'),
            'companyId' => Yii::t('app', 'Company'),
            'photo' => Yii::t('app', 'Photo'),
            'formPhoto' => Yii::t('app', 'Photo'),
            'balance' => Yii::t('app', 'Balance'),
            'works' => Yii::t('app', 'Works'),
            'formRoles' => Yii::t('app', 'Roles and permissions'),
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
        $this->works = array_filter($this->works);
        $this->formPhoto = UploadedFile::getInstance($this, 'formPhoto');



        return $load;
    }

    public function afterFind()
    {

        //$this->qualifications = UserService::getQualificationIds($this->id);
        $this->works = UserService::getWorksIds($this->id);
        $this->formRoles = ArrayHelper::getColumn($this->roles_rl, 'name');

        if($this->scenario != self::SCENARIO_SEARCH){
            $this->balance = TransactionService::getBalanceByUserId($this->id);
        }

        parent::afterFind();



    }

    function extraFields()
    {
        return ArrayHelper::merge(parent::extraFields(), [
            'name' => 'first_name',
            'secondName' => 'second_name',
            'lastName' => 'last_name',
            'photo' => function(){
                return $this->getPhotoUrl();
            },
            'fcmTokens' => function(){
                return (self::getService())::getFcmTokens($this->id);
            },
            'qualificationsAndWorks' => function(){
                return (self::getService())::getCategoriedWorks($this->id);
            },
            'balance' => function(){
                return TransactionService::getBalanceByUserId($this->id);
            },
        ]);
    }

    // =================================================================

    static function getImageUrl($filename)
    {
        if (empty($filename)) {
            return null;
        }
        return self::PATH_IMAGES . $filename;
    }

    static function findByEmail(string $email): ?self
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    static function findByPasswordResetToken(string $token): ?self
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    static function isPasswordResetTokenValid(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }



    public function getFullName()
    {
        return $this->first_name . ' ' . $this->second_name . ' ' . $this->last_name;
    }

    public function getPhoneString(): string
    {
        if (empty($this->phone)) {
            return '';
        }
        return preg_replace('/(\d{3})(\d{3})(\d{4})/', '+7($1)-$2-$3', $this->phone);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword(string $password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function getPhotoUrl(bool $absoluteUrl = true): ?string
    {
        if (empty($this->photo)) {
            return null;
        }

        /** @var string|null $photoUrl */
        $photoUrl = self::getImageUrl($this->photo);
        if ($photoUrl === null || $absoluteUrl == false) {
            return $photoUrl;
        }

        return (string)(Url::base(true) . $photoUrl);
    }

    /**
     * @return User|null
     * @throws SafeException
     * @throws \yii\web\NotFoundHttpException
     */
    public function saveImage(): ?self
    {

        return (self::getService())::saveImage($this->id, $this->formPhoto);
//        $oldFileName = $this->photo;
//
//        $uploadedFile = $this->formPhoto;
//
//        $extension = UploadFileHelper::getExtensionByMime($uploadedFile->type, false);
//        $fileName = UploadFileHelper::generateFileName($extension);
//
//        if (!empty($uploadedFile->content)) {
//            UploadFileHelper::createFileFromBase64($fileName, $uploadedFile->content);
//        } else {
//            $filePath = UploadFileHelper::getFilePath($fileName);
//            if (!move_uploaded_file($uploadedFile->tempName, $filePath)) {
//                return null;
//            }
//        }
//
//        UserRep::updatePhoto($this, $fileName);
//
//        if ($oldFileName) {
//            $oldFilePath = UploadFileHelper::getFilePath($oldFileName);
//            UploadFileHelper::deleteFile($oldFilePath);
//        }
//
//        return $this;
    }

    //=============== RELATIONS ============================================================

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getRoles_rl()
    {
        return $this->hasMany(AuthItems::class, ['name' => 'item_name'])->viaTable(AuthAssignment::tableName(), ['user_id' => 'id']);
    }

    // ===== IdentityInterface ========================================

    /**
     * @param $token
     * @param null $type
     * @return IdentityInterface|null
     * @throws SafeException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public static function findIdentityByAccessToken($token, $type = null): ?IdentityInterface
    {

        $token = AuthTokenRep::getByToken($token);
        if ($token === null) {
            throw new SafeException(401, Yii::t('app', 'invalid credentials'));
        }

        switch ($token->type) {
            case AuthToken::TYPE_CUSTOMER:
                $identity = UserRep::getByType($token->getUserId(), User::TYPE_CUSTOMER);
                break;
            case AuthToken::TYPE_EMPLOYEE:
                $identity = UserRep::getByType($token->getUserId(), User::TYPE_EMPLOYEE);
                break;
            default:
                $identity = null;
                break;
        }

        if ($identity === null) {
            throw new SafeException(401, Yii::t('app', 'invalid credentials'));
        }

        return $identity;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id): ?IdentityInterface
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): string
    {
        return $this->auth_key ?: '';
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }


    // ===== CRUDControllerModelInterface =============================

    /**
     * @return mixed|UserService
     */
    static function getService()
    {
        return UserService::class;
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
        $query->select('*');
        //var_dump($query);die();


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC
                ]
            ],
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

        // Баланс
        if(1){
            TransactionService::setBalanceQueryToUser($query);

            $balanceVal=null;
            $operator = '=';
            if($this->balance){

                if(stristr($this->balance, ' ')){
                    $parts = explode(' ', $this->balance);
                    if(isset($parts[0])) $operator = $parts[0];
                    if(isset($parts[1])) $balanceVal = (float)$parts[1];
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
                    $balanceVal = (float)$this->balance;
                }

            }


            $query
                ->filterWhere([$operator, '(COALESCE(plus.plus,0) - COALESCE(minus.minus,0))', $balanceVal]);
        }

        $query
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['ilike', 'first_name', $this->first_name])
            ->andFilterWhere(['ilike', 'second_name', $this->second_name])
            ->andFilterWhere(['ilike', 'last_name', $this->last_name])
            ->andFilterWhere(['=', 'status', $this->status])
            ->andFilterWhere(['=', 'type', $this->type])
            ->andFilterWhere(['=', 'id', $this->id])
            ->andFilterWhere(['or',
                ['ilike', 'first_name', $this->searchTerm],
                ['ilike', 'second_name', $this->searchTerm],
                ['ilike', 'last_name', $this->searchTerm],
            ])
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
            'email:email',
            'phone',
            [
                'attribute' => 'name',
                'label' => Yii::t('app', 'First Name'),
                'value' => function (self $model) {
                    return $model->first_name;
                },
            ],
            [
                'attribute' => 'secondName',
                'label' => Yii::t('app', 'Second Name'),
                'value' => function (self $model) {
                    return $model->second_name;
                },
            ],
            [
                'attribute' => 'lastName',
                'label' => Yii::t('app', 'Last Name'),
                'value' => function (self $model) {
                    return $model->last_name;
                },
            ],
            [
                'attribute' => 'type',
                'filter' => array_map(function ($el) {
                    return Yii::t('app', $el);
                }, self::TYPES),
                'value' => function (self $model) {
                    return Yii::t('app', self::TYPES[$model->type] ?? null);
                }
            ],
            [
                'attribute' => 'status',
                'filter' => array_map(function ($el) {
                    return Yii::t('app', $el);
                }, self::STATUSES),
                'value' => function (self $model) {
                    return Yii::t('app', self::STATUSES[$model->status] ?? null);
                }
            ],
            'balance:currency',
            [
                'attribute' => 'created_at',
                'label' => Yii::t('app', 'Creation date'),
                'format' => 'datetime',
                'value' => function (self $model) {
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
                        return !(bool)$model->deleted_at AND Yii::$app->user->ch('/user/delete');
                    },
                    'update' => function ($model) {
                        return !(bool)$model->deleted_at AND Yii::$app->user->ch('/user/update');
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
                        'visible' => Yii::$app->user->ch('/user/create'),
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
            'enableClientValidation' => false,
        ];

        return Yii::$app->controller->renderPartial('@app/views/forms/user', [
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