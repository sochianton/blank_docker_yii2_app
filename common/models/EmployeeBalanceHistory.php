<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%employee_balance_history}}".
 *
 * @property int $id
 * @property int $amount
 * @property int $user_id
 * @property int $type
 * @property string $created_at
 * @property string $updated_at
 */
class EmployeeBalanceHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%employee_balance_history}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount', 'user_id', 'type'], 'default', 'value' => null],
            [['amount', 'user_id', 'type'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'amount' => Yii::t('app', 'Amount'),
            'user_id' => Yii::t('app', 'User ID'),
            'type' => Yii::t('app', 'Type'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
