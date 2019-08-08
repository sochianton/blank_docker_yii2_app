<?php

namespace common\service;

use backend\models\forms\WorkCreateForm;
use common\dto\WorkDto;
use common\models\Work;
use common\repository\QualificationRepository;
use common\repository\WorkQualificationRepository;
use common\repository\WorkRepository;
use Yii;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class WorkService
 * @package common\service
 */
class WorkService
{
    /** @var WorkRepository $qualificationRepository */
    private $workRepository;
    /** @var WorkQualificationRepository $workQualificationRepository */
    private $workQualificationRepository;
    /** @var QualificationRepository $qualificationRepository */
    private $qualificationRepository;

    /**
     * WorkService constructor.
     * @param WorkRepository $workRepository
     * @param WorkQualificationRepository $workQualificationRepository
     * @param QualificationRepository $qualificationRepository
     */
    public function __construct(
        WorkRepository $workRepository,
        WorkQualificationRepository $workQualificationRepository,
        QualificationRepository $qualificationRepository
    ) {
        $this->workRepository = $workRepository;
        $this->workQualificationRepository = $workQualificationRepository;
        $this->qualificationRepository = $qualificationRepository;
    }

    /**
     * @param int $id
     * @return Work|null
     * @throws NotFoundHttpException
     */
    public function get(int $id): ?Work
    {
        if (!$work = $this->workRepository->get($id)) {
            throw new NotFoundHttpException(Yii::t('app', 'Work not found'));
        }

        return $work;
    }

    /**
     * @param int|null $category
     * @return WorkDto[]
     */
    public function getAll(int $category = null): array
    {
        if ($category) {
            $workIds = $this->workQualificationRepository->getWorkIdsByQualifications([(int)$category]);
        }
        $works = $this->workRepository->getAllList($workIds ?? []);

        return array_map(function (Work $work) {
            $work = $work->toArray();
            $work['qualificationIds'] = $this->getQualificationIds($work['id']);
            return new WorkDto($work);
        }, $works);
    }

    /**
     * @return array
     */
    public function getList(): array
    {
        $works = $this->workRepository->getAllList();

        return ArrayHelper::map($works, 'id', 'name');
    }

    /**
     * @param int $id
     * @return array
     */
    public function getQualificationIds(int $id): array
    {
        return $this->workQualificationRepository->getQualificationIds($id);
    }

    /**
     * @param WorkCreateForm $form
     * @return Work
     * @throws \Throwable
     */
    public function create(WorkCreateForm $form): Work
    {
        /** @var WorkDto $workDto */
        $workDto = $form->getDto();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $work = Work::create($workDto);
            $work = $this->workRepository->insert($work);

            $qualificationIds = $workDto->getQualificationIds();

            if (!empty($qualificationIds)) {
                $this->workQualificationRepository->insertAll($work->id, $qualificationIds);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        }

        return $work;
    }

    /**
     * @param int $workId
     * @param WorkDto $workDto
     * @return Work
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function update(int $workId, WorkDto $workDto): Work
    {
        /** @var Work $work */
        $work = $this->workRepository->get($workId);
        if ($work === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Work not found'));
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $work->name = $workDto->getName();
            $work->price = $workDto->getPrice();
            $work->commission = $workDto->getCommission();

            $work = $this->workRepository->update($work);

            $this->workQualificationRepository->deleteAll($workId);

            $qualificationIds = $workDto->getQualificationIds();

            if (!empty($qualificationIds)) {
                $this->workQualificationRepository->insertAll($work->id, $qualificationIds);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        }

        return $work;
    }

    /**
     * @param int $id
     * @return Work|null
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function block(int $id): ?Work
    {
        $work = $this->get($id);
        $work->deleted_at = time();
        $work = $this->workRepository->update($work);

        return $work;
    }

    /**
     * @param int $id
     * @return Work|null
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function restore(int $id): ?Work
    {
        $work = $this->get($id);
        $work->deleted_at = null;
        $work = $this->workRepository->update($work);

        return $work;
    }

}
