<?php

namespace common\repository;

use common\models\Qualification;
use Yii;

/**
 * Class QualificationRepository
 * @package common\repository
 */
class QualificationRepository
{
    /**
     * @param int $id
     * @return Qualification|null
     */
    public function get(int $id): ?Qualification
    {
        return Qualification::findOne(['id' => $id]);
    }

    /**
     * @param string $partOfName
     * @return Qualification[]
     */
    public function searchByName(string $partOfName): array
    {
        return Qualification::find()->where(['like', 'name', $partOfName])->all();
    }

    /**
     * @param bool $withDeleted
     * @param array $selected
     * @return Qualification[]
     */
    public function getList(bool $withDeleted = false, array $selected = []): array
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
     * @param Qualification $qualification
     * @param bool $runValidation
     * @return Qualification
     * @throws \Throwable
     */
    public function insert(Qualification $qualification, bool $runValidation = true): Qualification
    {
        if (!$qualification->insert($runValidation)) {
            throw new \Exception(Yii::t('errors', 'Cant create qualification'));
        }
        return $qualification;
    }

    /**
     * @param Qualification $qualification
     * @param bool $runValidation
     * @return Qualification
     * @throws \Exception
     */
    public function save(Qualification $qualification, bool $runValidation = true): Qualification
    {
        try {
            $qualification->save($runValidation);
            return $qualification;
        } catch (\Exception $error) {
            throw new \Exception(400, $error->getMessage());
        }
    }
}
