<?php

namespace common\ar;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%euroservice_integrate}}".
 *
 * @property int $bid_id
 * @property int $rid
 */
class EuroserviceIntegrate extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%euroservice_integrate}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bid_id', 'rid'], 'required'],
            [['bid_id', 'rid'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bid_id' => Yii::t('app', 'Bid ID'),
            'rid' => Yii::t('app', 'Request ID'),
        ];
    }
}
