<?php

namespace api\modules\employee\v1\service;

use api\modules\employee\v1\dto\LoginDto;
use common\ar\User;
use common\ar\AuthToken;
use common\repositories\AuthTokenRep;
use common\repositories\UserRep;
use common\repository\AuthTokenRepository;
use common\repository\EmployeeRepository;
use scl\tools\rest\exceptions\SafeException;
use Yii;
use yii\base\Exception;
use yii\db\StaleObjectException;

/**
 * Class AuthService
 * @package api\modules\v1\service
 */
class AuthService
{
    /** @var AuthTokenRepository $authTokenRepository */
    protected $authTokenRepository;
    /** @var EmployeeRepository $employeeRepository */
    protected $employeeRepository;

    /**
     * AuthService constructor.
     * @param AuthTokenRepository $authTokenRepository
     * @param EmployeeRepository $employeeRepository
     */
    public function __construct(
        AuthTokenRepository $authTokenRepository,
        EmployeeRepository $employeeRepository
    ) {
        $this->employeeRepository = $employeeRepository;
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
        //$employee = $this->employeeRepository->getByEmail($loginDto->email);
        $employee = UserRep::getByEmail($loginDto->email, User::TYPE_EMPLOYEE);
        if ($employee === null) {
            throw new SafeException(404, Yii::t('app', 'Emloyee not found'));
        }

        if (!$employee->validatePassword($loginDto->password)) {
            throw new SafeException(422, Yii::t('app', 'Invalid password'));
        }

        AuthTokenRep::deleteAllExpired($employee->id, \common\ar\AuthToken::TYPE_EMPLOYEE);

        $authToken = AuthToken::create($employee->id, AuthToken::TYPE_EMPLOYEE);
        if (AuthTokenRep::insert($authToken) === null) {
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

        $authToken = AuthTokenRep::get($token, AuthToken::TYPE_EMPLOYEE);
        if ($authToken === null) {
            throw new SafeException(401, Yii::t('app', 'You are not logged in'));
        }

        return AuthTokenRep::delete($authToken);
    }
}
