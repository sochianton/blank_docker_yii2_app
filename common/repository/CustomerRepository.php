<?php

namespace common\repository;

use common\models\Customer;
use yii\db\StaleObjectException;

/**
 * Class CustomerRepository
 * @package common\repository
 */
class CustomerRepository
{
    /**
     * @param int $id
     * @return Customer|null
     */
    public function get(int $id): ?Customer
    {
        return Customer::findOne(['id' => $id]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function isBlocked(int $id): bool
    {
        return (bool)Customer::find()
            ->where(['id' => $id])
            ->andWhere(['status' => Customer::STATUS_DELETED])
            ->count();
    }

    /**
     * @return array
     */
    public function getAllList(): array
    {
        return Customer::find()
            ->all();
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getList(array $ids): array
    {
        return Customer::findAll(['id' => $ids]);
    }

    /**
     * @param string $email
     * @return Customer|null
     */
    public function getByEmail(string $email): ?Customer
    {
        return Customer::findOne(['email' => $email]);
    }

    /**
     * @param Customer $customer
     * @param bool $runValidation
     * @return Customer|null
     * @throws \Throwable
     */
    public function insert(Customer $customer, bool $runValidation = true): ?Customer
    {
        if (!$customer->insert($runValidation)) {
            return null;
        }
        return $customer;
    }

    /**
     * @param Customer $customer
     * @param bool $runValidation
     * @return Customer|null
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function update(Customer $customer, bool $runValidation = true): ?Customer
    {
        return $customer->update($runValidation) !== false ? $customer : null;
    }

    /**
     * @param Customer $customer
     * @param string $photo
     * @return Customer|null
     */
    public function updatePhoto(Customer $customer, string $photo): ?Customer
    {
        $customer->updateAttributes(['photo' => $photo]);

        return $customer;
    }
}
