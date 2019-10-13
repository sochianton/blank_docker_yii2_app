<?php

namespace backend\models\forms;

use common\ar\User;
use common\dto\BidDto;
use common\models\Bid;
use common\models\BidAttachment;
use common\models\Customer;
use common\models\Employee;
use common\models\Work;
use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\web\UploadedFile;

/**
 * Class BidCreateForm
 * @package backend\models\forms
 */
class BidCreateForm extends Model
{
    /**
     * @var int $id
     */
    public $id;
    /**
     * @var string $name
     */
    public $name;
    /**
     * @var int
     */
    public $customerId;
    /**
     * @var int
     */
    public $employeeId;
    /**
     * @var int
     */
    public $status;
    /**
     * @var integer $price
     */
    public $price;
    /**
     * @var string $object
     */
    public $object;
    /**
     * @var string $customerComment
     */
    public $customerComment;
    /**
     * @var string $employeeComment
     */
    public $employeeComment;
    /**
     * @var integer $completeAt
     */
    public $completeAt;
    /**
     * @var array $works
     */
    public $works;
    /**
     * @var array|UploadedFile[] $customerPhotos
     */
    public $customerPhotos;
    /**
     * @var array|UploadedFile[] $employeePhotos
     */
    public $employeePhotos;
    /**
     * @var array|UploadedFile[] $files
     */
    public $files;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name', 'customerId', 'status', 'price', 'object', 'completeAt', 'works'], 'required'],
            [
                ['customerId'],
                'exist',
                'targetClass' => User::class,
                'filter' => ['type' => User::TYPE_CUSTOMER],
                'targetAttribute' => 'id'
            ],
            [
                ['employeeId'],
                'exist',
                'targetClass' => User::class,
                'filter' => ['type' => User::TYPE_EMPLOYEE],
                'targetAttribute' => 'id'
            ],
            [['price'], 'integer', 'min' => 1, 'max' => 2147483647],
            [['price'], 'default', 'value' => 0],
            [['name', 'object'], 'string', 'max' => 100],
            [['customerComment', 'employeeComment'], 'string', 'max' => 500],
            ['status', 'in', 'range' => array_keys(Bid::STATUSES)],
            [['completeAt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['id', 'price', 'customerId', 'employeeId', 'status'], 'integer'],
            ['works', 'integer'],
            [
                'works',
                'exist',
                'targetClass' => Work::class,
                'targetAttribute' => 'id'
            ],
            [
                ['customerPhotos'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'png, jpg, jpeg',
                'maxFiles' => BidAttachment::MAX_PHOTOS_CUSTOMER
            ],
            [
                ['employeePhotos'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'png, jpg, jpeg',
                'maxFiles' => BidAttachment::MAX_PHOTOS_EMPLOYEE
            ],
            [
                ['files'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'pdf, doc, docx, xls, xlsx, xlsm',
                'maxFiles' => BidAttachment::MAX_FILES
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'customerId' => Yii::t('app', 'Customer'),
            'employeeId' => Yii::t('app', 'Employee'),
            'status' => Yii::t('app', 'Status'),
            'price' => Yii::t('app', 'Price'),
            'object' => Yii::t('app', 'Object'),
            'customerComment' => Yii::t('app', 'Customer Comment'),
            'employeeComment' => Yii::t('app', 'Employee Comment'),
            'completeAt' => Yii::t('app', 'Complete At'),
            'works' => Yii::t('app', 'Works'),
            'customerPhotos' => Yii::t('app', 'Customer Photos'),
            'employeePhotos' => Yii::t('app', 'Employee Photos'),
            'files' => Yii::t('app', 'Files'),
        ];
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
     * @return BidDto
     */
    public function getDto(): BidDto
    {
        $dto = new BidDto(
            0,
            (string)$this->name,
            (int)$this->customerId,
            (int)$this->employeeId,
            (int)$this->status,
            (int)$this->price,
            (string)$this->object,
            $this->customerComment,
            $this->employeeComment,
            $this->completeAt,
            null,
            null,
            array_filter((array)$this->works),
            (array)$this->customerPhotos,
            (array)$this->files,
            (array)$this->employeePhotos
        );

        return $dto;
    }
}
