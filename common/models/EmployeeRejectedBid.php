<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "employee_rejected_bid".
 *
 * @property int $employee_id
 * @property int $bid_id
 */
class EmployeeRejectedBid extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employee_rejected_bid';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee_id', 'bid_id'], 'required'],
            [['employee_id', 'bid_id'], 'default', 'value' => null],
            [['employee_id', 'bid_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'employee_id' => Yii::t('app', 'Employee ID'),
            'bid_id' => Yii::t('app', 'Bid ID'),
        ];
    }

    /**
     * @param int $employeeId
     * @param int $bidId
     * @return EmployeeRejectedBid
     */
    public static function create(int $employeeId, int $bidId): self
    {
        return new static([
            'employee_id' => $employeeId,
            'bid_id' => $bidId
        ]);
    }
}
