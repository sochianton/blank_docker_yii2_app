<?php

namespace common\service;

use common\dto\CompanyDto;
use common\models\Company;
use common\repository\CompanyRepository;
use Yii;
use yii\db\Expression;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class CompanyService
 *
 * @package common\service
 */
class CompanyService
{
    /** @var CompanyRepository $companyRepository */
    protected $companyRepository;

    /**
     * CompanyService constructor.
     * @param CompanyRepository $companyRepository
     */
    public function __construct(
        CompanyRepository $companyRepository
    ) {
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param int $companyId
     * @return Company|null
     * @throws NotFoundHttpException
     */
    public function get(int $companyId): ?Company
    {
        $company = $this->companyRepository->get($companyId);
        if ($company === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Company not found'));
        }

        return $company;
    }

    /**
     * @param int|null $type
     * @return array
     */
    public function getList(int $type = null)
    {
        $companies = $this->companyRepository->getList($type);
        return ArrayHelper::map($companies, 'id', 'name');
    }

    /**
     * @param CompanyDto $companyDto
     * @return Company
     * @throws ServerErrorHttpException
     * @throws \Throwable
     */
    public function create(CompanyDto $companyDto): Company
    {
        $company = Company::create($companyDto);

        $company = $this->companyRepository->insert($company);
        if ($company === null) {
            throw new ServerErrorHttpException(Yii::t('app', 'Can`t create company for unknown reason'));
        }

        return $company;
    }

    /**
     * @param int $companyId
     * @param CompanyDto $companyDto
     * @return Company
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function update(int $companyId, CompanyDto $companyDto): Company
    {
        /** @var Company $company */
        $company = $this->companyRepository->get($companyId);
        if ($company === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Company not found'));
        }

        $company->status = $companyDto->getStatus();
        $company->type = $companyDto->getType();
        $company->name = $companyDto->getName();
        $company->address = $companyDto->getAddress();
        $company->number_of_contract = $companyDto->getNumberOfContract();
        $company->updated_at = new Expression('NOW()');

        $company = $this->companyRepository->update($company);
        if ($company === null) {
            throw new ServerErrorHttpException(Yii::t('app', 'Can\'t update company'));
        }

        return $company;
    }

    /**
     * @param int $id
     * @return Company|null
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function block(int $id): ?Company
    {
        $company = $this->get($id);
        $company->status = Company::STATUS_BLOCKED;
        $company = $this->companyRepository->update($company);

        return $company;
    }

    /**
     * @param int $id
     * @return Company|null
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function restore(int $id): ?Company
    {
        $company = $this->get($id);
        $company->status = Company::STATUS_ACTIVE;
        $company = $this->companyRepository->update($company);

        return $company;
    }
}
