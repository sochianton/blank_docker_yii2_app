<?php

namespace common\ar;

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
    public static function tableName(): string
    {
        return '{{%work_qualification}}';
    }

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

    public function attributeLabels(): array
    {
        return [
            'work_id' => Yii::t('app', 'Work ID'),
            'qualification_id' => Yii::t('app', 'Qualification ID'),
        ];
    }

}
