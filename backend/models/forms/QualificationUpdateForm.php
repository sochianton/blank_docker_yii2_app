<?php

namespace backend\models\forms;


use common\dto\QualificationDto;
use common\models\Qualification;

class QualificationUpdateForm extends QualificationCreateForm
{
    /** @var integer $is_deleted */
    public $is_deleted;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string'],
            ['name', 'string', 'max' => 100],
            [['id', 'is_deleted'], 'integer'],
        ];
    }

    /**
     * @return QualificationDto
     */
    public function getDto(): QualificationDto
    {
        $dto = new QualificationDto();

        $dto->setId($this->id);
        $dto->setName($this->name);
        $dto->setDeletedAt($this->is_deleted ? time() : null);

        return $dto;
    }

    /**
     * @param Qualification $qualification
     */
    public function fillFromModel(Qualification $qualification): void
    {
        $this->id = $qualification->id;
        $this->name = $qualification->name;
        $this->is_deleted = !empty($qualification->deleted_at);
    }
}
