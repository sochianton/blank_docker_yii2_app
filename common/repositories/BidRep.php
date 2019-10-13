<?php


namespace common\repositories;


use common\ar\Bid;
use common\ar\BidAttachment;
use common\ar\BidWork;
use common\ar\EmployeeRejectedBid;
use common\interfaces\BaseRepositoryInterface;
use common\traits\RepositoryTrait;

class BidRep implements BaseRepositoryInterface
{

    static $class = Bid::class;

    use RepositoryTrait;

    /**
     * @param int $id
     * @param int $type
     * @return array
     */
    static function getFiles(int $id, int $type): array
    {
        return BidAttachment::find()->where([
            'bid_id' => $id,
            'type' => $type
        ])->all();
    }

    /**
     * @param int $bidId
     * @return array
     */
    static function getWorkIds(int $bidId): array
    {
        return BidWork::find()
            ->where(['bid_id' => $bidId])
            ->select('work_id')
            ->column();
    }

    /**
     * @param Bid $bid
     * @param int $status
     * @return Bid
     */
    static function setStatus(Bid $bid, int $status): Bid
    {
        $bid->updateAttributes(['status' => $status]);

        return $bid;
    }

    /**
     * @param int $bidId
     */
    static function deleteAllEmployeeRejects(int $bidId): void
    {
        EmployeeRejectedBid::deleteAll(['bid_id' => $bidId]);
    }

    /**
     * @param $id
     * @param $employeeId
     * @return Bid|null
     */
    static function getForEmployee($id, $employeeId): ?Bid{
        return Bid::find()
            ->where(['id' => $id, 'employee_id' => $employeeId, 'deleted_at' => null])
            ->one();
    }

    /**
     * @param $id
     * @param $customerId
     * @return Bid|null
     */
    static function getForCustomer($id, $customerId): ?Bid{
        return Bid::find()
            ->where(['id' => $id, 'customer_id' => $customerId, 'deleted_at' => null])
            ->one();
    }



    /**
     * @param $id
     * @return Bid|null
     */
    static function getNew($id): ?Bid{
        return Bid::find()
            ->where(['id' => $id, 'status' => Bid::STATUS_NEW, 'deleted_at' => null])
            ->one();
    }

    /**
     * @param int $id
     * @param int $type
     * @return int
     */
    static function getFilesCount(int $id, int $type): int
    {
        return BidAttachment::find()->where([
            'bid_id' => $id,
            'type' => $type
        ])->count();
    }

    /**
     * @param array $files
     * @throws \Exception
     */
    static function saveFiles(array $files): void
    {
        $model = new BidAttachment();
        try {
            \Yii::$app->db->createCommand()->batchInsert(
                BidAttachment::tableName(),
                $model->attributes(),
                $files
            )->execute();
        } catch (\Exception $exception) {
            throw new \Exception(\Yii::t('errors', 'Can\'t upload'));
        }
    }

}