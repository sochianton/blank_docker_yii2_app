<?php

namespace common\repository;

use common\models\Bid;
use common\models\BidAttachment;
use Yii;

/**
 * Class BidRepository
 * @package common\repository
 */
class BidRepository
{
    /**
     * @param int $id
     * @return Bid|null
     */
    public function get(int $id): ?Bid
    {
        return Bid::findOne($id);
    }

    /**
     * @param int|null $startDate
     * @param int|null $endDate
     * @param int|null $customerId
     * @return array
     */
    public function getListAll(
        ?string $startDate,
        ?string $endDate,
        ?int $customerId = null
    ): array {
        $query = Bid::find();

        if (false AND $startDate && $endDate) {
            $query->andFilterWhere([
                'between',
                'complete_at',
                $startDate,
                $endDate
            ]);
        }

        if ($customerId) {
            $query->andFilterWhere(['customer_id' => $customerId]);
        }

        return $query->orderBy('complete_at DESC')->all();
    }

    /**
     * @param int $id
     * @param int $customerId
     * @return Bid|null
     */
    public function getCustomer(int $id, int $customerId): ?Bid
    {
        return Bid::find()
            ->where(['id' => $id])
            ->andWhere(['customer_id' => $customerId])
            ->one();
    }

    /**
     * @param int $id
     * @param int $employeeId
     * @return Bid|null
     */
    public function getEmployee(int $id, int $employeeId): ?Bid
    {
        return Bid::find()
            ->where(['id' => $id])
            ->andWhere(['employee_id' => $employeeId])
            ->one();
    }

    /**
     * @param int $customerId
     * @param bool $isArchive
     * @return Bid[]
     */
    public function getListCustomer(int $customerId, bool $isArchive = false): array
    {
        $query = Bid::find()
            ->where(['customer_id' => $customerId])
            ->orderBy(['updated_at' => SORT_DESC]);

        if ($isArchive) {
            $query->andFilterWhere(['status' => Bid::STATUSES_ARCHIVE]);
        } else {
            $query->andFilterWhere(['status' => Bid::STATUSES_ACTIVE]);
        }

        return $query->all();
    }

    /**
     * @param int $customerId
     * @param string $term
     * @param int|null $status
     * @return array
     */
    public function searchCustomer(int $customerId, string $term, ?int $status = null): array
    {
        $query = Bid::find()
            ->where(['customer_id' => $customerId])
            ->andWhere(['ilike', 'name', $term]);

        if ($status != null) {
            $query->andFilterWhere(['status' => $status]);
        }

        return $query->all();
    }

    /**
     * @param int $employeeId
     * @param bool $isArchive
     * @return Bid[]
     */
    public function getListEmployee(int $employeeId, bool $isArchive = false)
    {
        $query = Bid::find()
            ->where(['employee_id' => $employeeId])
            ->orderBy(['updated_at' => SORT_DESC]);

        if ($isArchive) {
            $query->andFilterWhere(['status' => Bid::STATUSES_ARCHIVE]);
        } else {
            $query->andFilterWhere(['status' => Bid::STATUSES_ACTIVE]);
        }

        return $query->all();
    }

    /**
     * @param int $employeeId
     * @param string $term
     * @param int|null $status
     * @return array
     */
    public function searchEmployee(int $employeeId, string $term, ?int $status = null): array
    {
        $query = Bid::find()
            ->where(['employee_id' => $employeeId])
            ->andWhere(['ilike', 'name', $term]);

        if ($status != null) {
            $query->andFilterWhere(['status' => $status]);
        }

        return $query->all();
    }

    /**
     * @param Bid $bid
     * @param bool $runValidation
     * @return Bid|null
     * @throws \Throwable
     */
    public function insert(Bid $bid, bool $runValidation = true): ?Bid
    {
        if (!$bid->insert($runValidation)) {
            return null;
        }
        return $bid;
    }

    /**
     * @param Bid $bid
     * @param bool $runValidation
     * @return Bid|null
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function update(Bid $bid, bool $runValidation = true): ?Bid
    {
        return $bid->update($runValidation) !== false ? $bid : null;
    }

    /**
     * @param Bid $bid
     * @param int $status
     * @return Bid
     */
    public function setStatus(Bid $bid, int $status): Bid
    {
        $bid->updateAttributes(['status' => $status]);

        return $bid;
    }

    /**
     * @param int $id
     * @param int $type
     * @return array
     */
    public function getFiles(int $id, int $type): array
    {
        return BidAttachment::find()->where([
            'bid_id' => $id,
            'type' => $type
        ])->all();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function deleteFile(string $name): bool
    {
        return (bool)BidAttachment::deleteAll(['name' => $name]);
    }

    /**
     * @param int $id
     * @param int $type
     * @return int
     */
    public function getFilesCount(int $id, int $type): int
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
    public function saveFiles(array $files): void
    {
        $model = new BidAttachment();
        try {
            Yii::$app->db->createCommand()->batchInsert(
                BidAttachment::tableName(),
                $model->attributes(),
                $files
            )->execute();
        } catch (\Exception $exception) {
            throw new \Exception(Yii::t('errors', 'Can\'t upload'));
        }
    }
}
