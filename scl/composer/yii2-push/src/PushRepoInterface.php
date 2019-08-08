<?php

/**
 * Created by PhpStorm.
 * User: alexsh
 * Date: 29.10.18
 * Time: 17:19
 */
namespace scl\yii\push;

interface PushRepoInterface
{
    /**
     * Get token list by user id list
     * Do not return empty string
     * @param int[] $uidList
     * @param int $usersType
     * @return string[]
     */
    public function getPushTokenListById(array $uidList, int $usersType): array;

    /**
     * Remove tokens from storage by token list
     * @param string[] $tokenList
     * @return bool true for successful, false - otherwise
     */
    public function removeTokensById(array $tokenList): bool;
}
