<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%employee}}".
 *
 * @property int $id
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $phone [varchar(20)]
 * @property string $first_name
 * @property string $second_name
 * @property string $last_name
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $photo [varchar(255)]
 * @property string $balance [integer]
 * @property string $company_id [integer]
 */
class Employee extends User
{
    const BALANCE_DENOMINATOR = 100;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%employee}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [
                [
                    'password_hash',
                    'email',
                    'first_name',
                    'second_name',
                    'last_name',
                    'status',
                    'created_at',
                    'updated_at'
                ],
                'required'
            ],
            [['status', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['status', 'company_id'], 'integer'],
            [['balance'], 'integer', 'min' => 0],
            [
                ['password_hash', 'password_reset_token', 'email', 'first_name', 'second_name', 'last_name', 'photo'],
                'string',
                'max' => 255
            ],
            [['email'], 'unique'],
            [['phone'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'email' => Yii::t('app', 'Email'),
            'phone' => Yii::t('app', 'Phone'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'first_name' => Yii::t('app', 'First Name'),
            'second_name' => Yii::t('app', 'Second Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'photo' => Yii::t('app', 'Photo'),
            'balance' => Yii::t('app', 'Balance'),
        ];
    }

    /**
     * @return float
     */
    public function getBalance(): float
    {
        return $this->balance;
    }
}
