<?php

namespace common\services;

use common\interfaces\BaseServiceInterface;
use common\repositories\CompanyRep;
use common\traits\ServiceTrait;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use common\ar\Company;

class CompanyService implements BaseServiceInterface
{

    use ServiceTrait;

    /** @var  CompanyRep*/
    static $repository = CompanyRep::class;

    /**
     * @param int $id
     * @return Company|null
     * @throws NotFoundHttpException
     */
    static function block(int $id): ?Company
    {
        $company = self::get($id);
        $company->status = Company::STATUS_BLOCKED;
        if($company->save()){
            return $company;
        }

        return null;
    }

    /**
     * @param int $id
     * @return Company|null
     * @throws NotFoundHttpException
     */
    static function restore(int $id): ?Company
    {

        $company = self::get($id);
        $company->status = Company::STATUS_ACTIVE;
        if($company->save()){
            return $company;
        }

        return null;
    }

    static function getListArr(): array{

        return ArrayHelper::map((self::$repository)::getAllActiveList(), 'id', 'name');

    }

}