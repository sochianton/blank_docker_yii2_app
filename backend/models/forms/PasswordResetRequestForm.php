<?php

namespace backend\models\forms;

use Yii;
use yii\base\Model;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    /** @var string $email */
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required', 'message' => Yii::t('app', 'Email cannot be blank.')],
            ['email', 'email'],
        ];
    }

}
