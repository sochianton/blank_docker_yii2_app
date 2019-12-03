<?php


namespace common\repositories;


use common\ar\BidWork;
use common\ar\Qualification;
use common\ar\User;
use common\ar\UserWork;
use common\ar\Work;
use common\ar\WorkQualification;
use common\interfaces\BaseRepositoryInterface;
use common\traits\RepositoryTrait;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;

class WorkRep implements BaseRepositoryInterface
{

    static $class = Work::class;

    use RepositoryTrait;

    /**
     * @param $workId
     * @return array
     */
    static function getQualificationIds($workId){

        return WorkQualification::find()
            ->where(['work_id' => $workId])
            ->select('qualification_id')
            ->column();

    }

    /**
     * @param int $workId
     * @param array $qualificationIds
     * @throws \Exception
     */
    static function insertQualificationArray(int $workId, array $qualificationIds): void
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
     * @param array $workIds
     * @return int
     */
    static function deleteQualificationArray(array $workIds): int
    {
        return WorkQualification::deleteAll(['work_id' => $workIds]);
    }

    /**
     * @param array $workIds
     * @return Work[]
     */
    static function getAll(array $workIds = []): array
    {
        $query = Work::find();

        if (!empty($workIds)) {
            $query->where(['id' => $workIds]);
        }

        return $query->all();
    }

    /**
     * @return ActiveQuery
     */
    static function getAllWithCatsQuery() : Query{
        return (new Query())
            ->from(['w' => Work::tableName()])
            ->leftJoin(['cw' => WorkQualification::tableName()], 'cw.work_id = w.id')
            ->leftJoin(['c' => Qualification::tableName()], 'cw.qualification_id = c.id')
            ;
    }

    /**
     * @return array
     */
    static function getAllWithCats() : array{
        return self::getAllWithCatsQuery()
            ->select('w.*, c.name c_name')
            ->all()
            ;
    }

    /**
     * @param $userId
     * @return array
     */
    static function getAllWithCatsByIserId($userId) : array{
        return self::getAllWithCatsQuery()
            ->leftJoin(['uw' => UserWork::tableName()], 'uw.work_id = w.id')
            ->where(['uw.user_id' => $userId])
            ->select('w.*, c.name c_name')
            ->all()
            ;
    }

    /**
     * @param int $bidId
     * @return Work[]
     */
    static function getWorksByBidId(int $bidId): array
    {
        return Work::find()
            ->from(['w' => Work::tableName()])
            ->select(['w.*'])
            ->innerJoin(['bw' => BidWork::tableName()], 'bw.work_id = w.id')
            ->where(['bw.bid_id' => $bidId])
            ->all();
    }

    /**
     * @param int $catId
     * @return Work[]
     */
    static function getWorksByCategoryId(int $catId): array
    {
        return Work::find()
            ->from(['w' => Work::tableName()])
            ->select(['w.*'])
            ->innerJoin(['cw' => WorkQualification::tableName()], 'cw.work_id = w.id')
            ->where(['cw.qualification_id' => $catId])
            ->all();
    }

    /**
     * @param array $names
     * @return Work[]
     */
    static function getWorksByNames(array $names): array
    {
        $query = Work::find();

        $query->where(['name' => $names]);

        return $query->all();
    }



    /**
     * @param int $bidId
     * @param array $workIds
     * @throws \Exception
     */
    static function insertAllByBid(int $bidId, array $workIds): void
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
    static function deleteAllByBid(int $bidId): void
    {
        BidWork::deleteAll(['bid_id' => $bidId]);
    }

}