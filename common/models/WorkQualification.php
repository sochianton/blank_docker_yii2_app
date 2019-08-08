<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Qualification model
 *
 * @property integer $work_id
 * @property integer $qualification_id
 */
class WorkQualification extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%work_qualification}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['work_id', 'qualification_id'], 'required'],
            [['work_id', 'qualification_id'], 'integer'],
            ['work_id', 'exist', 'targetClass' => Work::class, 'targetAttribute' => ['work_id' => 'id']],
            [
                'qualification_id',
                'exist',
                'targetClass' => Qualification::class,
                'targetAttribute' => ['qualification_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'work_id' => Yii::t('app', 'Work ID'),
            'qualification_id' => Yii::t('app', 'Qualification ID'),
        ];
    }

    /***
     * @param int $workId
     * @param int $qualificationId
     * @return WorkQualification
     */
    public static function create(int $workId, int $qualificationId): self
    {
        $model = new static();
        $model->work_id = $workId;
        $model->qualification_id = $qualificationId;

        return $model;
    }

}
