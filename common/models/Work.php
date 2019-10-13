<?php

namespace common\models;

use common\dto\WorkDto;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class Work
 * @package common\models
 * @property string $id [integer]
 * @property string $name [varchar(100)]
 * @property string $price [integer]
 * @property string $commission [integer]
 * @property string $deleted_at [integer]
 * @property string $created_at [integer]
 * @property string $updated_at [integer]
 */
class Work extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%work}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at'
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'price', 'commission'], 'required'],
            ['name', 'string', 'max' => 100],
            [['commission'], 'integer', 'min' => 0],
            ['commission', 'integer', 'max' => 100],
            [['created_at', 'updated_at', 'deleted_at'], 'integer'],
            [['price'], 'double'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'price' => Yii::t('app', 'Price'),
            'commission' => Yii::t('app', 'Commission'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /***
     * @param WorkDto $workDto
     * @return Work
     */
    public static function create(WorkDto $workDto): self
    {
        $model = new static();
        $model->name = $workDto->getName();
        $model->price = $workDto->getPrice();
        $model->commission = $workDto->getCommission();

        return $model;
    }

}
