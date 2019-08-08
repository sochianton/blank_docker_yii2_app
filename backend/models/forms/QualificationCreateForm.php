<?php

namespace backend\models\forms;


use common\dto\QualificationDto;
use common\models\Qualification;
use Yii;
use yii\base\Model;

class QualificationCreateForm extends Model
{
    /** @var int $id */
    public $id;
    /** @var string $name */
    public $name;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string'],
            ['name', 'string', 'max' => 100],
            [['id'], 'integer'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'is_deleted' => Yii::t('app', 'Is deleted?'),
        ];
    }

    /**
     * @return QualificationDto
     */
    public function getDto(): QualificationDto
    {
        $dto = new QualificationDto();

        $dto->setName($this->name);

        return $dto;
    }

    /**
     * @param Qualification $qualification
     */
    public function fillFromModel(Qualification $qualification): void
    {
        $this->id = $qualification->id;
        $this->name = $qualification->name;
    }
}
