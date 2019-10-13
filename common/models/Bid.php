<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "{{%bid}}".
 *
 * @property int $id
 * @property string $name [varchar(255)]
 * @property int $customer_id
 * @property int $employee_id
 * @property int $status
 * @property int $price
 * @property string $object
 * @property int $complete_at [integer]
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted_at [integer]
 * @property string $customer_comment
 * @property string $employee_comment
 */
class Bid extends ActiveRecord
{
    const EVENT_CREATE_BID_BY_CUSTOMER = 'bid_create_customer';
    const EVENT_APPLY_BID_BY_EMPLOYEE = 'bid_apply_employee';
    const EVENT_REJECT_BID_BY_EMPLOYEE = 'bid_reject_employee';
    const EVENT_DONE_BID_BY_EMPLOYEE = 'bid_done_employee';
    const EVENT_BID_CANCELED = 'bid_canceled';



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

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%bid}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'in', 'range' => array_keys(self::STATUSES)],
            [['name', 'customer_id', 'status', 'complete_at'], 'required'],
            [
                [
                    'customer_id',
                    'employee_id',
                    'status',
                    'price',
                    'deleted_at'
                ],
                'integer'
            ],
            [['complete_at'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['name', 'object'], 'string', 'max' => 255],
            [['price'], 'integer', 'min' => 1, 'max' => 2147483647],
            [['customer_comment', 'employee_comment'], 'string', 'max' => 500],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'employee_id' => Yii::t('app', 'Employee ID'),
            'status' => Yii::t('app', 'Status'),
            'price' => Yii::t('app', 'Price'),
            'object' => Yii::t('app', 'Object'),
            'customer_comment' => Yii::t('app', 'Customer Comment'),
            'employee_comment' => Yii::t('app', 'Employee Comment'),
            'complete_at' => Yii::t('app', 'Complete At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
        ];
    }

    /**
     * @param string $name
     * @param int $customerId
     * @param int $employeeId
     * @param int $status
     * @param int $price
     * @param int $completeAt
     * @param string $object
     * @param string $customerComment
     * @param string|null $employeeComment
     * @return Bid
     */
    public static function create(
        string $name,
        int $customerId,
        ?int $employeeId,
        int $status,
        int $price,
        $completeAt,
        string $object,
        ?string $customerComment,
        ?string $employeeComment
    ): self {
        return new static([
            'name' => $name,
            'customer_id' => $customerId,
            'employee_id' => $employeeId,
            'status' => $status,
            'price' => $price,
            'complete_at' => $completeAt,
            'object' => $object,
            'customer_comment' => $customerComment,
            'employee_comment' => $employeeComment,
        ]);
    }
}
