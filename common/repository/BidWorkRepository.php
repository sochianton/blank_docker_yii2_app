<?php

namespace common\repository;

use common\models\BidWork;
use Yii;

/**
 * Class BidWorkRepository
 * @package common\repository
 */
class BidWorkRepository
{
    /**
     * @param int $bidId
     * @return array
     */
    public function getWorkIds(int $bidId): array
    {
        return BidWork::find()
            ->where(['bid_id' => $bidId])
            ->select('work_id')
            ->column();
    }

    /**
     * @param int $bidId
     * @param array $workIds
     * @throws \Exception
     */
    public function insertAll(int $bidId, array $workIds): void
    {
        $model = new BidWork();
        try {
            $bidWorks = [];
            foreach ($workIds as $workId) {
                $bidWorks[] = [
                    'bid_id' => $bidId,
                    'work_id' => $workId,
                ];
            }

            Yii::$app->db->createCommand()->batchInsert(
                BidWork::tableName(),
                $model->attributes(),
                $bidWorks
            )->execute();
        } catch (\Exception $exception) {
            throw new \Exception(Yii::t('errors', 'Can\'t create bid works'));
        }
    }

    /**
     * @param int $bidId
     */
    public function deleteAll(int $bidId): void
    {
        BidWork::deleteAll(['bid_id' => $bidId]);
    }
}
