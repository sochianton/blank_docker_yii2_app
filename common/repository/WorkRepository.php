<?php

namespace common\repository;

use common\models\BidWork;
use common\models\Work;
use common\models\WorkQualification;
use Throwable;
use Yii;
use yii\db\StaleObjectException;

class WorkRepository
{
    /**
     * @param int $id
     * @return Work|null
     */
    public function get(int $id): ?Work
    {
        return Work::findOne(['id' => $id]);
    }

    /**
     * @param array $workIds
     * @return array
     */
    public function getAllList(array $workIds = []): array
    {
        $query = Work::find();

        if (!empty($workIds)) {
            $query->where(['id' => $workIds]);
        }

        return $query->all();
    }


    /**
     * @param int $bidId
     * @return Work[]
     */
    public function getWorksByBidId(int $bidId): array
    {
        return Work::find()
            ->from(['w' => Work::tableName()])
            ->select(['w.*'])
            ->innerJoin(['bw' => BidWork::tableName()], 'bw.work_id = w.id')
            ->where(['bw.bid_id' => $bidId])
            ->all();
    }

    /**
     * @param Work $work
     * @param bool $runValidation
     * @return Work
     * @throws Throwable
     */
    public function insert(Work $work, bool $runValidation=true): Work
    {
        if (!$work->insert($runValidation)) {
            throw new \Exception(Yii::t('errors', 'Cant create work'));
        }
        return $work;
    }

    /**
     * @param Work $work
     * @param bool $runValidation
     * @return Work|null
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function update(Work $work, bool $runValidation = true): ?Work
    {
        return $work->update($runValidation) !== false ? $work : null;
    }
}
