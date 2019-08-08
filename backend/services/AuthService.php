<?php

namespace backend\services;

use backend\models\forms\PasswordResetRequestForm;
use common\models\Admin;
use common\models\User;
use common\repository\AdminRepository;
use Yii;

/**
 * Class AuthService
 * @package backend\services
 */
class AuthService
{
    /**
     * @var AdminRepository
     */
    private $adminRepository;

    /**
     * AuthService constructor.
     * @param AdminRepository $adminRepository
     */
    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    /**
     * @param PasswordResetRequestForm $model
     * @return bool
     * @throws \yii\base\Exception
     */
    public function sendNewPassword(PasswordResetRequestForm $model): bool
    {
        /* @var $user Admin */
        $user = $this->adminRepository->getByEmail($model->email);
        if ($user === null || $user->status !== User::STATUS_ACTIVE) {
            return false;
        }

        $password = Yii::$app->security->generateRandomString(10);
        $this->adminRepository->setPassword($user->id, $password);

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user, 'password' => $password]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($model->email)
            ->setSubject(Yii::t('app', 'Password reset for ') . Yii::$app->name)
            ->send();
    }

}
