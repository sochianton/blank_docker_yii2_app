<?php

namespace common\services;

use common\interfaces\BaseServiceInterface;
use common\repositories\TransactionRep;
use common\traits\ServiceTrait;
use yii\db\ActiveQuery;

class TransactionService implements BaseServiceInterface
{

    use ServiceTrait;

    /** @var  TransactionRep */
    static $repository = TransactionRep::class;

    /**
     * @param int $uId
     * @return float
     */
    static function getBalanceByUserId(int $uId) :float {

        return (self::$repository)::getBalanceByUserId($uId);

    }


    static function setBalanceQueryToUser(ActiveQuery $q) :ActiveQuery {

        return (self::$repository)::setBalanceQueryToUser($q);

    }

}