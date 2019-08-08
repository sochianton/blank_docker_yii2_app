<?php

namespace common\repository;

use common\models\WorkQualification;
use Yii;

/**
 * Class WorkQualificationRepository
 * @package common\repository
 */
class WorkQualificationRepository
{
    /**
     * @param int $workId
     * @return array
     */
    public function getQualificationIds(int $workId): array
    {
        return WorkQualification::find()
            ->where(['work_id' => $workId])
            ->select('qualification_id')
            ->column();
    }

    /**
     * @param array $qualificationIds
     * @return array
     */
    public function getWorkIdsByQualifications(array $qualificationIds): array
    {
        return WorkQualification::find()
            ->select(['work_id'])
            ->where(['qualification_id' => $qualificationIds])
            ->column();
    }

    /**
     * @param WorkQualification $workQualification
     * @param bool $runValidation
     * @return WorkQualification
     * @throws \Throwable
     */
    public function insert(WorkQualification $workQualification, bool $runValidation = true): WorkQualification
    {
        if (!$workQualification->insert($runValidation)) {
            throw new \Exception(Yii::t('errors', 'Can\'t create work qualification'));
        }
        return $workQualification;
    }

    /**
     * @param int $workId
     * @param array $qualificationIds
     * @throws \Exception
     */
    public function insertAll(int $workId, array $qualificationIds): void
    {
        $model = new WorkQualification();
        try {
            $workQualifications = [];
            foreach ($qualificationIds as $qualificationId) {
                $workQualifications[] = [
                    'work_id' => $workId,
                    'qualification_id' => $qualificationId,
                ];
            }

            Yii::$app->db->createCommand()->batchInsert(
                WorkQualification::tableName(),
                $model->attributes(),
                $workQualifications
            )->execute();
        } catch (\Exception $exception) {
            throw new \Exception(Yii::t('errors', 'Can\'t create work qualifications'));
        }
    }

    /**
     * @param int $workId
     */
    public function deleteAll(int $workId): void
    {
        WorkQualification::deleteAll(['work_id' => $workId]);
    }
}
