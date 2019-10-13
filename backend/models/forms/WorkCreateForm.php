<?php

namespace backend\models\forms;

use common\dto\WorkDto;
use common\models\Qualification;
use Yii;
use yii\base\Model;

class WorkCreateForm extends Model
{
    /**
     * @var string $name
     */
    public $name;
    /**
     * @var integer $price
     */
    public $price;
    /**
     * @var integer $commission
     */
    public $commission;
    /**
     * @var array $qualifications
     */
    public $qualifications;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name', 'commission', 'qualifications'], 'required'],
            [['price'], 'default', 'value' => 0],
            ['name', 'string', 'max' => 100],
            [['commission', 'qualifications'], 'integer'],
            [['price'], 'string'],
            [
                'qualifications',
                'exist',
                'targetClass' => Qualification::class,
                'targetAttribute' => 'id'
            ]
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'price' => Yii::t('app', 'Price'),
            'commission' => Yii::t('app', 'Commission'),
            'qualifications' => Yii::t('app', 'Qualifications'),
            'deletedAt' => Yii::t('app', 'Deleted At'),
        ];
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
        $dto->setQualificationIds(array_filter([(int)$this->qualifications]));

        return $dto;
    }
}
