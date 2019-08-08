<?php

namespace common\models;

use common\repository\AuthTokenRepository;
use common\repository\CustomerRepository;
use common\repository\EmployeeRepository;
use scl\tools\rest\exceptions\SafeException;
use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Url;
use yii\web\IdentityInterface;

/**
 * User model
 */
abstract class User extends ActiveRecord implements IdentityInterface
{
    const PATH_IMAGES = '/images/';

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const STATUSES = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_DELETED => 'Blocked',
    ];

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
     * Validates password
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     * @param string $password
     * @throws Exception
     */
    public function setPassword(string $password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

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
        /** @var AuthTokenRepository $authTokenRepository */
        $authTokenRepository = Yii::$container->get(AuthTokenRepository::class);
        /** @var AuthToken $token */
        $token = $authTokenRepository->getByToken($token);
        if ($token === null) {
            throw new SafeException(401, Yii::t('app', 'invalid credentials'));
        }

        switch ($token->type) {
            case AuthToken::TYPE_CUSTOMER:
                /** @var CustomerRepository $customerRepository */
                $customerRepository = Yii::$container->get(CustomerRepository::class);
                $identity = $customerRepository->get($token->getUserId());
                break;
            case AuthToken::TYPE_EMPLOYEE:
                /** @var EmployeeRepository $employeeRepository */
                $employeeRepository = Yii::$container->get(EmployeeRepository::class);
                $identity = $employeeRepository->get($token->getUserId());
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
        return $this->auth_key ?? '';
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->first_name . ' ' . $this->second_name . ' ' . $this->last_name;
    }

    /**
     * @param $filename
     * @return string|null
     */
    public static function getImageUrl($filename)
    {
        if (empty($filename)) {
            return null;
        }
        return self::PATH_IMAGES . $filename;
    }

    /**
     * @param bool $absoluteUrl
     * @return string|null
     */
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
     * @return string
     */
    public function getPhoneString(): string
    {
        if (empty($this->phone)) {
            return '';
        }
        return preg_replace('/(\d{3})(\d{3})(\d{4})/', '+7($1)-$2-$3', $this->phone);
    }

    /**
     * @param string $email
     * @param string $password
     * @param string|null $phone
     * @param string $name
     * @param string $secondName
     * @param string $lastName
     * @param int $status
     * @param int $companyId
     * @return Customer|Employee
     * @throws Exception
     */
    public static function create(
        string $email,
        string $password,
        ?string $phone,
        string $name,
        string $secondName,
        string $lastName,
        int $status,
        ?int $companyId
    ): self {
        return new static([
            'email' => $email,
            'phone' => $phone,
            'password_hash' => Yii::$app->security->generatePasswordHash($password),
            'first_name' => $name,
            'second_name' => $secondName,
            'last_name' => $lastName,
            'status' => $status,
            'company_id' => $companyId,
            'created_at' => new Expression('NOW()'),
            'updated_at' => new Expression('NOW()'),
        ]);
    }
}
