<?php

namespace common\ar;

use Yii;
use yii\db\ActiveRecord;

/**
 * Class AuthToken
 * @package common\models
 *
 * @property string $token
 * @property int $user_id
 * @property int $type
 * @property string $expired_at
 */
class AuthToken extends ActiveRecord
{
    const TYPE_CUSTOMER = 1;
    const TYPE_EMPLOYEE = 2;
    const TYPE_ADMINISTRATOR = 3;
    const TYPES = [
        self::TYPE_CUSTOMER,
        self::TYPE_EMPLOYEE,
        self::TYPE_ADMINISTRATOR
    ];

    const EXPIRED_AT = '+30 days';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%auth_token}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['token', 'user_id', 'type', 'expired_at'], 'required'],
            [['user_id', 'type'], 'default', 'value' => null],
            [['user_id', 'type'], 'integer'],
            [['expired_at'], 'safe'],
            [['token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'token' => Yii::t('app', 'Token'),
            'user_id' => Yii::t('app', 'User ID'),
            'type' => Yii::t('app', 'Type'),
            'expired_at' => Yii::t('app', 'Expired At'),
        ];
    }

    /**
     * @param int $userId
     * @param int $type
     * @return AuthToken
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public static function create(int $userId, int $type): self
    {
        return new static([
            'token' => Yii::$app->security->generateRandomString(32),
            'user_id' => $userId,
            'type' => $type,
            'expired_at' => (new \DateTime())->modify(self::EXPIRED_AT)->format("Y-m-d H:i:s"),
        ]);
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isEmployee(): bool
    {
        return $this->type === self::TYPE_EMPLOYEE;
    }

    /**
     * @return bool
     */
    public function isCustomer(): bool
    {
        return $this->type === self::TYPE_CUSTOMER;
    }
}
