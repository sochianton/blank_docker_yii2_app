<?php

namespace backend\models\forms;

use common\dto\WorkDto;
use common\models\Work;

class WorkUpdateForm extends WorkCreateForm
{
    /**
     * @var int $id
     */
    public $id;
    /**
     * @var int $deletedAt
     */
    public $deletedAt;

    /**
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            [['id', 'deletedAt'], 'integer'],
        ];
        $rules = array_merge($rules, parent::rules());

        return $rules;
    }

    /**
     * @return WorkDto
     */
    public function getDto(): WorkDto
    {
        $dto = new WorkDto();

        $dto->setName($this->name);
        $dto->setPrice($this->price);
        $dto->setCommission($this->commission);
        $dto->setQualificationIds([(int)$this->qualifications]);
        $dto->setDeletedAt($this->deletedAt);

        return $dto;
    }

    /**
     * @param Work $work
     * @param array $qualifications
     */
    public function fillFromModel(Work $work, array $qualifications): void
    {
        $this->id = $work->id;
        $this->name = $work->name;
        $this->price = $work->price;
        $this->commission = $work->commission;
        $this->deletedAt = $work->deleted_at;
        $this->qualifications = $qualifications;
    }
}
