<?php

namespace common\repositories;


use common\ar\Push;
use scl\yii\push\PushRepoInterface;

class PushRep implements PushRepoInterface
{

    /**
     * Get token list by user id list
     * Do not return empty string
     * @param array $uidList
     * @param int $usersType
     * @return array
     */
    public function getPushTokenListById(array $uidList, $usersType=null): array
    {
        return Push::find()
            ->select('token')
            ->where([
                'user_id' => $uidList,
                //'user_type' => $usersType
            ])
            ->column();
    }

    /**
     * Remove tokens from storage by token list
     * @param string[] $tokenList
     * @return bool true for successful, false - otherwise
     */
    public function removeTokensById(array $tokenList): bool
    {
        return Push::deleteAll(['token' => $tokenList]);
    }

    /**
     * @param int $userId
     * @return bool
     */
    static function removeTokensByUserId(int $userId): bool
    {
        return Push::deleteAll(['user_id' => $userId]);
    }

    /**
     * @param int $userId
     * @param int $userType
     * @param string $token
     * @return bool
     */
    static function add(int $userId, int $userType, string $token): bool
    {
        $model = new Push();
        $model->user_id = $userId;
        $model->user_type = $userType;
        $model->token = $token;
        return $model->save();
    }
}
