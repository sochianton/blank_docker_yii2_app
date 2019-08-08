<?php

namespace common\repository;

use common\models\Admin;
use Yii;
use yii\base\Exception;

/**
 * Class UserRepository
 * @package common\repository
 */
class AdminRepository
{
    /**
     * @param int $id
     * @return Admin|null
     */
    public function get(int $id): ?Admin
    {
        return Admin::findOne(['id' => $id]);
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getList(array $ids): array
    {
        return Admin::findAll(['id' => $ids]);
    }

    /**
     * @param string $email
     * @return Admin|null
     */
    public function getByEmail(string $email): ?Admin
    {
        return Admin::findOne(['email' => $email]);
    }

    /**
     * @param string $id
     * @param string $password
     * @return Admin|null
     * @throws Exception
     */
    public function setPassword(string $id, string $password): ?Admin
    {
        $passwordHash = Yii::$app->security->generatePasswordHash($password);
        $admin = $this->get($id);
        if ($admin === null) {
            return null;
        }
        $admin->updateAttributes(['password_hash' => $passwordHash]);

        return $admin;
    }
}
