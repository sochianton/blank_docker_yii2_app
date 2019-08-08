<?php

namespace common\repository;

use common\models\Employee;
use yii\db\Expression;
use yii\db\StaleObjectException;

/**
 * Class EmployeeRepository
 * @package common\repository
 */
class EmployeeRepository
{
    /**
     * @param int $id
     * @return Employee|null
     */
    public function get(int $id): ?Employee
    {
        return Employee::findOne(['id' => $id]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function isBlocked(int $id): bool
    {
        return (bool)Employee::find()
            ->where(['id' => $id])
            ->andWhere(['status' => Employee::STATUS_DELETED])
            ->count();
    }

    /**
     * @param array $includedEmployeeIds
     * @param array $excludedEmployeeIds
     * @return Employee|null
     */
    public function getFirstAvailable(array $includedEmployeeIds, array $excludedEmployeeIds): ?Employee
    {
        return Employee::find()
            ->where(['id' => $includedEmployeeIds])
            ->andFilterWhere(['NOT IN', 'id', $excludedEmployeeIds])
            ->andWhere(['!=', 'status', Employee::STATUS_DELETED])
            ->orderBy(new Expression('random()'))
            ->one();
    }

    /**
     * @return array
     */
    public function getAllList(): array
    {
        return Employee::find()
            ->all();
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getList(array $ids): array
    {
        return Employee::findAll(['id' => $ids]);
    }

    /**
     * @param string $email
     * @return Employee|null
     */
    public function getByEmail(string $email): ?Employee
    {
        return Employee::findOne(['email' => $email]);
    }

    /**
     * @param Employee $employee
     * @param bool $runValidation
     * @return Employee|null
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function update(Employee $employee, bool $runValidation = true): ?Employee
    {
        return $employee->update($runValidation) !== false ? $employee : null;
    }

    /**
     * @param int $companyId
     * @return array
     */
    public function getListByCompanyId(int $companyId): array
    {
        return Employee::findAll(['company_id' => $companyId]);
    }

    /**
     * @param string $email
     * @return bool
     */
    public function isEmailAvailable(string $email): bool
    {
        return Employee::findOne(['email' => $email]) === null;
    }

    /**
     * @param Employee $employee
     * @param bool $runValidation
     * @return Employee
     * @throws \Throwable
     */
    public function insert(Employee $employee, bool $runValidation = true): ?Employee
    {
        if (!$employee->insert($runValidation)) {
            return null;
        }
        return $employee;
    }

    /**
     * @param Employee $employee
     * @param bool $runValidation
     * @return Employee
     * @throws \Exception
     */
    public function save(Employee $employee, bool $runValidation = true): Employee
    {
        try {
            $employee->save($runValidation);
            return $employee;
        } catch (\Exception $error) {
            throw new \Exception(400, $error->getMessage());
        }
    }

    /**
     * @param Employee $employee
     * @param string $photo
     * @return Employee|null
     */
    public function updatePhoto(Employee $employee, string $photo): ?Employee
    {
        $employee->updateAttributes(['photo' => $photo]);

        return $employee;
    }

    /**
     * @param Employee $employee
     * @param int $price
     * @param int $commission
     * @return Employee|null
     */
    public function transferFundsToBalance(Employee $employee, int $price, int $commission): ?Employee
    {
        $amount = $price - ($price * ($commission / 100));
        $balance = bcadd($employee->balance, $amount);
        $employee->updateAttributes(['balance' => $balance]);

        return $employee;
    }
}
