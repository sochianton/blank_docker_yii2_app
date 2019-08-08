<?php

namespace common\repository;

use common\models\EmployeeQualification;
use Yii;

/**
 * Class EmployeeQualificationRepository
 * @package common\repository
 */
class EmployeeQualificationRepository
{
    /**
     * @param int $employeeId
     * @return array
     */
    public function getQualifications(int $employeeId): array
    {
        return EmployeeQualification::find()
            ->select(['qualification_id'])
            ->where(['employee_id' => $employeeId])
            ->column();
    }

    /**
     * @param array $qualificationIds
     * @return array
     */
    public function getEmployeeIdsByQualifications(array $qualificationIds): array
    {
        return EmployeeQualification::find()
            ->select(['employee_id'])
            ->where(['qualification_id' => $qualificationIds])
            ->column();
    }

    /**
     * @param int $id
     * @return array
     */
    public function getQualificationIds(int $id): array
    {
        return EmployeeQualification::find()
            ->where(['employee_id' => $id])
            ->select('qualification_id')
            ->column();
    }

    /**
     * @param EmployeeQualification $employeeQualification
     * @param bool $runValidation
     * @return EmployeeQualification
     * @throws \Throwable
     */
    public function insert(
        EmployeeQualification $employeeQualification,
        bool $runValidation = true
    ): EmployeeQualification {
        if (!$employeeQualification->insert($runValidation)) {
            throw new \Exception(Yii::t('errors', 'Can\'t create employee qualification'));
        }
        return $employeeQualification;
    }

    /**
     * @param int $employeeId
     * @param array $qualificationIds
     * @throws \Exception
     */
    public function insertAll(int $employeeId, array $qualificationIds): void
    {
        $model = new EmployeeQualification();
        try {

            $employeeQualifications = [];
            foreach ($qualificationIds as $qualificationId) {
                $employeeQualifications[] = [
                    'employee_id' => $employeeId,
                    'qualification_id' => $qualificationId,
                ];
            }

            Yii::$app->db->createCommand()->batchInsert(
                EmployeeQualification::tableName(),
                $model->attributes(),
                $employeeQualifications
            )->execute();
        } catch (\Exception $exception) {
            throw new \Exception(Yii::t('errors', 'Can\'t create employee qualifications'));
        }
    }

    /**
     * @param int $employeeId
     */
    public function deleteAll(int $employeeId): void
    {
        EmployeeQualification::deleteAll(['employee_id' => $employeeId]);
    }
}
