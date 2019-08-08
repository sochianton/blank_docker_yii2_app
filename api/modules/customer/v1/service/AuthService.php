<?php

namespace api\modules\customer\v1\service;

use api\modules\customer\v1\dto\LoginDto;
use common\models\AuthToken;
use common\repository\AuthTokenRepository;
use common\repository\CustomerRepository;
use scl\tools\rest\exceptions\SafeException;
use Yii;
use yii\base\Exception;
use yii\db\StaleObjectException;

/**
 * Class AuthService
 * @package api\modules\v1\customer\service
 */
class AuthService
{
    /** @var AuthTokenRepository $authTokenRepository */
    protected $authTokenRepository;
    /** @var CustomerRepository $customerRepository */
    protected $customerRepository;

    /**
     * AuthService constructor.
     * @param AuthTokenRepository $authTokenRepository
     * @param CustomerRepository $customerRepository
     */
    public function __construct(
        AuthTokenRepository $authTokenRepository,
        CustomerRepository $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->authTokenRepository = $authTokenRepository;
    }

    /**
     * @param LoginDto $loginDto
     * @return string
     * @throws Exception
     * @throws SafeException
     * @throws \Throwable
     */
    public function login(LoginDto $loginDto): string
    {
        $customer = $this->customerRepository->getByEmail($loginDto->email);
        if ($customer === null) {
            throw new SafeException(404, Yii::t('app', 'Customer not found'));
        }

        if (!$customer->validatePassword($loginDto->password)) {
            throw new SafeException(422, Yii::t('app', 'Invalid password'));
        }

        $this->authTokenRepository->deleteAllExpired($customer->id, AuthToken::TYPE_CUSTOMER);

        $authToken = AuthToken::create($customer->id, AuthToken::TYPE_CUSTOMER);
        if ($this->authTokenRepository->insert($authToken) === null) {
            throw new SafeException(500,
                Yii::t('app-messages', 'Can`t create authentication token for unknown reason'));
        }

        return $authToken->token;
    }

    /**
     * @param string $token
     * @return bool
     * @throws SafeException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function logout(string $token): bool
    {
        $authToken = $this->authTokenRepository->get($token, AuthToken::TYPE_CUSTOMER);
        if ($authToken === null) {
            throw new SafeException(401, Yii::t('app', 'You are not logged in'));
        }

        return $this->authTokenRepository->delete($authToken);
    }
}
