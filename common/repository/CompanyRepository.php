<?php

namespace common\repository;

use common\models\Company;
use yii\db\Expression;

/**
 * Class CompanyRepository
 * @package common\repository
 */
class CompanyRepository
{
    /**
     * @param int $id
     * @param bool $withOutDeleted
     * @return Company|null
     */
    public function get(int $id, bool $withOutDeleted = false): ?Company
    {
        $query = Company::find()
            ->where(['id' => $id]);

        if ($withOutDeleted) {
            $query->andWhere(['deleted_at' => null]);
        }

        return $query->one();
    }

    /**
     * @param int|null $type
     * @return array
     */
    public function getList(?int $type): array
    {
        $query = Company::find();

        if ($type) {
            $query->andFilterWhere(['type' => $type]);
        }

        return $query->all();
    }

    /**
     * @param Company $company
     * @param bool $runValidation
     * @return Company|null
     * @throws \Throwable
     */
    public function insert(Company $company, bool $runValidation = true): ?Company
    {
        if (!$company->insert($runValidation)) {
            return null;
        }
        return $company;
    }

    /**
     * @param Company $company
     * @param bool $runValidation
     * @return Company|null
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function update(Company $company, bool $runValidation = true): ?Company
    {
        return $company->update($runValidation) !== false ? $company : null;
    }

    /**
     * @param Company $company
     */
    public function delete(Company $company): void
    {
        $company->updateAttributes(['deleted_at' => new Expression('NOW()')]);
    }
}
