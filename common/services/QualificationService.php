<?php

namespace common\services;

use common\interfaces\BaseServiceInterface;
use common\ar\Qualification;
use common\repositories\QualificationRep;
use common\traits\ServiceTrait;

class QualificationService implements BaseServiceInterface
{

    use ServiceTrait;

    /** @var  QualificationRep*/
    static $repository = QualificationRep::class;

    static function getDto(Qualification $bid): array{
        return $bid->toArray([
            'id',
            'name',
        ]);
    }

    /**
     * @param bool $withDeleted
     * @param array $selected
     * @return Qualification[]
     */
    static function getList(bool $withDeleted = false, array $selected = []): array
    {
        return QualificationRep::getList($withDeleted, $selected);
    }

    static function isExist(int $id): bool{
        return QualificationRep::isExist($id);
    }

}