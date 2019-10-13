<?php

namespace common\repositories;

use common\ar\Company;
use common\ar\Work;
use common\interfaces\BaseRepositoryInterface;
use common\traits\RepositoryTrait;
use yii\helpers\ArrayHelper;


class CompanyRep implements BaseRepositoryInterface
{

    use RepositoryTrait;

    /** @var Company string */
    static $class = Company::class;

    static function getAllActiveList(array $ids = []): array
    {
        $query = (self::$class)::find();

        if (!empty($ids)) {
            $query->where(['id' => $ids]);
        }
        $query->andWhere([
            'status' => (self::$class)::STATUS_ACTIVE
        ]);

        return $query->all();
    }

}