<?php

namespace common\ar;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "employee_qualification".
 *
 * @property int $employee_id
 * @property int $qualification_id
 */
class EmployeeQualification extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%employee_qualification}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee_id', 'qualification_id'], 'required'],
            [['employee_id', 'qualification_id'], 'default', 'value' => null],
            [['employee_id', 'qualification_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'employee_id' => Yii::t('app', 'Employee ID'),
            'qualification_id' => Yii::t('app', 'Qualification ID'),
        ];
    }
}
