<?php

namespace common\ar;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%bid_work}}".
 *
 * @property int $bid_id
 * @property int $work_id
 */
class BidWork extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%bid_work}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bid_id', 'work_id'], 'required'],
            [['bid_id', 'work_id'], 'default', 'value' => null],
            [['bid_id', 'work_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bid_id' => Yii::t('app', 'Bid ID'),
            'work_id' => Yii::t('app', 'Work ID'),
        ];
    }
}
