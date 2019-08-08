<?php

namespace common\models;

use common\dto\QualificationDto;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Qualification model
 *
 * @property integer $id
 * @property string $name
 * @property integer $deleted_at
 * @property integer $created_at
 * @property integer $updated_at
 */
class Qualification extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%qualification}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::className(),
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
            ['name', 'required'],
            ['name', 'string', 'max' => 100],
            [['created_at', 'updated_at', 'deleted_at'], 'integer']
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
            'deleted_at' => Yii::t('app', 'Deleted At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /***
     * @param QualificationDto $qualificationDto
     * @return Qualification
     */
    public static function create(QualificationDto $qualificationDto): self
    {
        $model = new static();
        $model->name = $qualificationDto->getName();

        return $model;
    }

}
