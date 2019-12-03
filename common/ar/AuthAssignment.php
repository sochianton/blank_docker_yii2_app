<?php

namespace common\ar;

use Yii;
use yii\db\ActiveRecord;
use yii\rbac\DbManager;

/**
 * @property string $item_name
 * @property string $user_id
 * @property integer $created_at
 *
 */
class AuthAssignment extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        /** @var DbManager $auth */
        $auth = Yii::$app->authManager;
        return $auth->assignmentTable;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_name', 'user_id'], 'required'],
            [['item_name', 'user_id'], 'string', 'max' => 64],
            [['created_at'], 'integer'],
            [['created_at'], 'default', 'value' => time()],


            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['item_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItems::class, 'targetAttribute' => ['item_name' => 'name']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Auth item name'),
            'user' => Yii::t('app', 'User'),
        ];
    }

}