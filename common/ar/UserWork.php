<?php

namespace common\ar;

use Yii;
use yii\db\ActiveRecord;

/**
 * UserWork model user_work
 *
 * @property integer $work_id
 * @property integer $user_id
 */
class UserWork extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%user_work}}';
    }

    public function rules(): array
    {
        return [
            [['work_id', 'user_id'], 'required'],
            [['work_id', 'user_id'], 'integer'],
            ['work_id', 'exist', 'targetClass' => Work::class, 'targetAttribute' => ['work_id' => 'id']],
            ['user_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'work_id' => Yii::t('app', 'Work ID'),
            'user_id' => Yii::t('app', 'User ID'),
        ];
    }

}
