<?php

namespace common\repositories;

use common\ar\Qualification;
use common\interfaces\BaseRepositoryInterface;
use common\ar\WorkQualification;
use common\traits\RepositoryTrait;



class QualificationRep implements BaseRepositoryInterface
{

    static $class = Qualification::class;

    use RepositoryTrait;

    /**
     * @param bool $withDeleted
     * @param array $selected
     * @return \common\models\Qualification[]
     */
    static function getList(bool $withDeleted = false, array $selected = []): array
    {
        $query = Qualification::find();

        if (!$withDeleted) {
            $query->where(['IS', 'deleted_at', null]);
        }

        if (!empty($selected)) {
            $query->where(['id' => $selected]);
        }

        return $query->all();
    }

    /**
     * @param int $id
     * @return bool
     */
    static function isExist(int $id): bool{
        $q = Qualification::find();
        $q->where(['id' => $id]);
        return (bool)$q->count();
    }

    /**
     * @param array $qualificationIds
     * @return array
     */
    static function getWorkIdsByQualifications(array $qualificationIds): array
    {
        return WorkQualification::find()
            ->select(['work_id'])
            ->where(['qualification_id' => $qualificationIds])
            ->column();
    }

    /**
     * @param int $workId
     * @return array
     */
    static function getQualificationIdsByWork($workId): array
    {
        return WorkQualification::find()
            ->where(['work_id' => $workId])
            ->select('qualification_id')
            ->column();
    }

}