<?php

namespace common\ar;

use Yii;
use yii\db\ActiveRecord;
use yii\rbac\DbManager;

/**
 * @property string $parent
 * @property string $child
 *
 * @property AuthItems $parent_rl
 * @property AuthItems $child_rl
 */
class AuthItemChild extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        /** @var DbManager $auth */
        $auth = Yii::$app->authManager;
        return $auth->itemChildTable;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent', 'child'], 'required'],
            [['parent', 'child'], 'string', 'max' => 64],
            [['parent', 'child'], 'unique', 'targetAttribute' => ['parent', 'child']],
            [['parent'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItems::class, 'targetAttribute' => ['parent' => 'name']],
            [['child'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItems::class, 'targetAttribute' => ['child' => 'name']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'parent' => Yii::t('app', 'Parent'),
            'child' => Yii::t('app', 'Child'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent_rl()
    {
        return $this->hasOne(AuthItems::class, ['name' => 'parent']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChild_rl()
    {
        return $this->hasOne(AuthItems::class, ['name' => 'child']);
    }
}