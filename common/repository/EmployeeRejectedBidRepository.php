<?php

namespace common\repository;

use common\models\EmployeeRejectedBid;

/**
 * Class EmployeeRejectedBidRepository
 * @package common\repository
 */
class EmployeeRejectedBidRepository
{
    /**
     * @param int $bidId
     * @return array
     */
    public function getEmployeeIdsByBidId(int $bidId): array
    {
        return EmployeeRejectedBid::find()
            ->where(['bid_id' => $bidId])
            ->column();
    }

    /**
     * @param EmployeeRejectedBid $employeeRejectedBid
     * @param bool $runValidation
     * @return EmployeeRejectedBid|null
     * @throws \Throwable
     */
    public function insert(EmployeeRejectedBid $employeeRejectedBid, bool $runValidation = true): ?EmployeeRejectedBid
    {
        if (!$employeeRejectedBid->insert($runValidation)) {
            return null;
        }
        return $employeeRejectedBid;
    }

    /**
     * @param int $bidId
     */
    public function deleteAll(int $bidId): void
    {
        EmployeeRejectedBid::deleteAll(['bid_id' => $bidId]);
    }
}
