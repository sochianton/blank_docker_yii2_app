<?php

namespace common\models;

use common\dto\CompanyDto;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "{{%company}}".
 *
 * @property int $id
 * @property int $type
 * @property int $status
 * @property string $name
 * @property string $address
 * @property string $number_of_contract [varchar(255)]
 * @property string $created_at
 * @property string $updated_at
 * @property int $deleted_at [timestamp(0)]
 */
class Company extends ActiveRecord
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

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%company}}';
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
            [['type', 'status', 'name', 'address'], 'required'],
            [['type', 'status'], 'default', 'value' => null],
            [['type', 'status', 'number_of_contract'], 'integer'],
            ['status', 'in', 'range' => array_keys(self::STATUSES)],
            ['type', 'in', 'range' => array_keys(self::TYPES)],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'address'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'status' => Yii::t('app', 'Status'),
            'name' => Yii::t('app', 'Name'),
            'address' => Yii::t('app', 'Address'),
            'number_of_contract' => Yii::t('app', 'Number Of Contract'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @param CompanyDto $companyDto
     * @return Company
     */
    public static function create(CompanyDto $companyDto): self
    {
        $status = $companyDto->getStatus();
        return new static([
            'type' => $companyDto->getType(),
            'status' => $status && in_array($status, array_keys(self::STATUSES)) ? $status : self::STATUS_ACTIVE,
            'name' => $companyDto->getName(),
            'address' => $companyDto->getAddress(),
            'number_of_contract' => $companyDto->getNumberOfContract(),
        ]);
    }
}
