<?php

namespace common\service;

use backend\models\forms\QualificationCreateForm;
use common\dto\QualificationDto;
use common\models\Qualification;
use common\repository\QualificationRepository;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class QualificationService
 * @package common\service
 */
class QualificationService
{
    /** @var QualificationRepository $qualificationRepository */
    protected $qualificationRepository;

    /**
     * QualificationService constructor.
     * @param QualificationRepository $qualificationRepository
     */
    public function __construct(QualificationRepository $qualificationRepository)
    {
        $this->qualificationRepository = $qualificationRepository;
    }

    /**
     * @param QualificationCreateForm $form
     * @return Qualification
     * @throws \Throwable
     */
    public function create(QualificationCreateForm $form): Qualification
    {
        /** @var QualificationDto $qualificationCreateForm */
        $qualificationCreateForm = $form->getDto();

        $qualification = Qualification::create($qualificationCreateForm);
        $qualification = $this->qualificationRepository->insert($qualification);

        return $qualification;
    }

    /**
     * @param int $id
     * @return Qualification|null
     * @throws NotFoundHttpException
     */
    public function get(int $id): ?Qualification
    {
        if (!$qualification = $this->qualificationRepository->get($id)) {
            throw new NotFoundHttpException(Yii::t('app', 'Qualification not found'));
        }

        return $qualification;
    }


    /**
     * @param bool $withDeleted
     * @param array $selected
     * @return Qualification[]
     */
    public function getList(bool $withDeleted = false, array $selected = []): array
    {
        return $this->qualificationRepository->getList($withDeleted, $selected);
    }

    /**
     * @param int $id
     * @return Qualification
     * @throws NotFoundHttpException
     */
    public function block(int $id): Qualification
    {
        $qualification = $this->get($id);
        $qualification->deleted_at = time();
        $qualification = $this->qualificationRepository->save($qualification);

        return $qualification;
    }

    /**
     * @param int $id
     * @return Qualification
     * @throws NotFoundHttpException
     */
    public function restore(int $id): Qualification
    {
        $qualification = $this->get($id);
        $qualification->deleted_at = null;
        $qualification = $this->qualificationRepository->save($qualification);

        return $qualification;
    }

    /**
     * @param QualificationDto $qualificationDto
     * @return Qualification
     * @throws NotFoundHttpException
     */
    public function update(QualificationDto $qualificationDto): Qualification
    {
        $qualification = $this->get($qualificationDto->getId());
        $qualification->name = $qualificationDto->getName();
        $qualification->deleted_at = $qualificationDto->getDeletedAt();

        $qualification = $this->qualificationRepository->save($qualification);

        return $qualification;
    }

    /**
     * @param string $partOfName
     * @return Qualification[]
     */
    public function search(string $partOfName): array
    {
        return $this->qualificationRepository->searchByName($partOfName);
    }
}
