<?php

namespace common\repository;

use common\models\AuthToken;
use yii\db\Expression;
use yii\db\StaleObjectException;

class AuthTokenRepository
{
    /**
     * @param string $token
     * @param string $type
     * @return AuthToken|null
     */
    public function get(string $token, string $type): ?AuthToken
    {
        return AuthToken::findOne([
            'token' => $token,
            'type' => $type,
        ]);
    }

    /**
     * @param string $token
     * @return AuthToken|null
     */
    public function getByToken(string $token): ?AuthToken
    {
        return AuthToken::findOne([
            'token' => $token,
        ]);
    }

    /**
     * @param int $userId
     * @param string $type
     * @return array
     */
    public function getByUserId(int $userId, string $type): array
    {
        return AuthToken::findAll([
            'user_id' => $userId,
            'type' => $type,
        ]);
    }

    /**
     * @param int $userId
     * @param string $type
     * @return array
     */
    public function getOneByUserId(int $userId, string $type): ?AuthToken
    {
        return AuthToken::findOne([
            'user_id' => $userId,
            'type' => $type,
        ]);
    }

    /**
     * @param AuthToken $token
     * @param bool $runValidation
     * @return AuthToken|null
     * @throws \Throwable
     */
    public function insert(AuthToken $token, bool $runValidation = true): ?AuthToken
    {
        if (!$token->insert($runValidation)) {
            return null;
        }

        return $token;
    }

    /**
     * @param AuthToken $token
     * @return bool
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function delete(AuthToken $token): bool
    {
        return $token->delete() !== false;
    }

    /**
     * @param int $userId
     * @param string $type
     */
    public function deleteAllExpired(int $userId, string $type): void
    {
        AuthToken::deleteAll([
            'and',
            ['user_id' => $userId],
            ['type' => $type],
            ['<', 'expired_at', new Expression('NOW()')],
        ]);
    }
}
