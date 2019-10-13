<?php

namespace common\repositories;

use common\ar\AuthToken;
use yii\db\Expression;
use yii\db\StaleObjectException;

class AuthTokenRep
{
    /**
     * @param string $token
     * @param string $type
     * @return AuthToken|null
     */
    static function get(string $token, string $type): ?AuthToken
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
    static function getByToken(string $token): ?AuthToken
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
    static function getByUserId(int $userId, string $type): array
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
    static function getOneByUserId(int $userId, string $type): ?AuthToken
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
    static function insert(AuthToken $token, bool $runValidation = true): ?AuthToken
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
    static function delete(AuthToken $token): bool
    {
        return $token->delete() !== false;
    }

    /**
     * @param int $userId
     * @param string $type
     */
    static function deleteAllExpired(int $userId, string $type): void
    {
        AuthToken::deleteAll([
            'and',
            ['user_id' => $userId],
            ['type' => $type],
            ['<', 'expired_at', new Expression('NOW()')],
        ]);
    }
}
