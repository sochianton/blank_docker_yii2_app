<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;


/**
 * Class Push
 * @package common\models
 * @property string $id [integer]
 * @property string $user_id [integer]
 * @property string $user_type [integer]
 * @property string $token [varchar(255)]
 * @property string $created_at [integer]
 */
class Push extends ActiveRecord
{
    const TYPE_CUSTOMER = 10;
    const TYPE_EMPLOYEE = 20;

    const TYPES = [
        self::TYPE_EMPLOYEE,
        self::TYPE_CUSTOMER
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%push_token}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => null,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['token', 'string'],
            ['token', 'unique'],
            [['token', 'user_id', 'user_type'], 'required'],
            ['user_type', 'in', 'range' => self::TYPES],
            [['created_at'], 'default', 'value' => null],
            [['created_at', 'user_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'token' => Yii::t('app', 'Token'),
            'user_id' => Yii::t('app', 'User Id'),
            'user_type' => Yii::t('app', 'User Type'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @param array $data
     * @return Push
     */
    public static function create(array $data): Push
    {
        $model = new static();
        $model->token = $data['token'] ?? '';
        $model->user_id = $data['user_id'] ?? null;
        $model->user_type = $data['user_type'] ?? null;
        $model->save();
        return $model;
    }
}
