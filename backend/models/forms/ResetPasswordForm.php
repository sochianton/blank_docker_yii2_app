<?php

namespace backend\models\forms;

use common\models\Admin;
use yii\base\Model;
use yii\web\BadRequestHttpException;

/**
 * Password reset form
 * Class ResetPasswordForm
 * @package backend\models\forms
 */
class ResetPasswordForm extends Model
{
    /** @var string $password */
    public $password;
    /** @var string $password_repeat */
    public $password_repeat;
    /** @var Admin $_user */
    private $_user;

    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws BadRequestHttpException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new BadRequestHttpException(\Yii::t('app', 'Password reset token cannot be blank.'));
        }

        $this->_user = Admin::findByPasswordResetToken($token);
        if (!$this->_user) {
            throw new BadRequestHttpException(\Yii::t('app', 'Wrong password reset token.'));
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'password_repeat'], 'required'],
            [
                'password_repeat',
                'compare',
                'compareAttribute' => 'password',
                'message' =>
                    \Yii::t('app', 'Passwords must match!')
            ]
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     * @throws \yii\base\Exception
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();

        return $user->save(false);
    }
}
