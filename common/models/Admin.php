<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * Admin model
 * @property string $id [integer]
 * @property string $email [varchar(255)]
 * @property string $password_hash [varchar(255)]
 * @property string $password_reset_token [varchar(255)]
 * @property string $first_name [varchar(255)]
 * @property string $second_name [varchar(255)]
 * @property string $last_name [varchar(255)]
 * @property int $status [smallint]
 * @property int $created_at [timestamp(0)]
 * @property int $updated_at [timestamp(0)]
 * @property string $auth_key [varchar(255)]
 */
class Admin extends User
{

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
            [['status', 'created_at', 'updated_at'], 'integer'],
            [
                [
                    'password_hash',
                    'password_reset_token',
                    'email',
                    'first_name',
                    'second_name',
                    'auth_key',
                    'last_name'
                ],
                'string',
                'max' => 255
            ],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
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
            'password_hash' => Yii::t('app', 'Password Hash'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'first_name' => Yii::t('app', 'First Name'),
            'second_name' => Yii::t('app', 'Second Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%admin}}';
    }

    /**
     * {@inheritdoc}
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null): ?IdentityInterface
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @param string $email
     * @return Admin|null
     */
    public static function findByEmail(string $email): ?Admin
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken(string $token): ?Admin
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     * @param string|null $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Generates "remember me" authentication key
     * @throws Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     * @throws Exception
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
