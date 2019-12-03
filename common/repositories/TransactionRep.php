<?php

namespace common\repositories;

use common\ar\Company;
use common\ar\User;
use common\interfaces\BaseRepositoryInterface;
use common\traits\RepositoryTrait;
use common\ar\Transactions;
use yii\db\ActiveQuery;


class TransactionRep implements BaseRepositoryInterface
{

    use RepositoryTrait;

    /** @var Company string */
    static $class = Transactions::class;

    static function getBalanceByUserId(int $uId) :float {


//        $res = (new \yii\db\Query())
//            ->select([
//                'plus' => (new \yii\db\Query())
//                    ->from((self::$class)::tableName())
//                    ->select([
//                        'minus' => 'SUM(amount)',
//                    ])
//                    ->where([
//                        'to' => $uId,
//                        'deleted_at' => null
//                    ])
//                    ->groupBy('to'),
//                'minus' => (new \yii\db\Query())
//                    ->from((self::$class)::tableName())
//                    ->select([
//                        //'minus' => 'NULLIF(SUM(amount),0)',
//                        'minus' => 'SUM(amount)',
//                    ])
//                    ->where([
//                        'from' => $uId,
//                        'deleted_at' => null
//                    ])
//                    ->groupBy('from'),
//            ])->all();


            return User::find()
                ->alias('u')
                ->select([
//                    'u.id',
//                    'plus.plus',
//                    'minus.minus',
                    'balance' => '(COALESCE(plus.plus,0) - COALESCE(minus.minus,0))',
                ])
                ->leftJoin(
                    [
                        'plus' => (new \yii\db\Query())
                            ->from((self::$class)::tableName())
                            ->select([
                                'to',
                                'plus' => 'SUM(amount)',
                            ])
                            ->where([
                                'deleted_at' => null
                            ])
                            ->groupBy('to')
                    ],
                    'u.id=plus.to'
                )
                ->leftJoin(
                    [
                        'minus' => (new \yii\db\Query())
                            ->from((self::$class)::tableName())
                            ->select([
                                'from',
                                'minus' => 'SUM(amount)',
                            ])
                            ->where([
                                'deleted_at' => null
                            ])
                            ->groupBy('from')
                    ],
                    'u.id=minus.from'
                )
                ->where(['u.id' => $uId])
                ->asArray()
                ->scalar();

         //var_dump($res);die();

    }

    /**
     * @param ActiveQuery $q
     * @return ActiveQuery
     */
    static function setBalanceQueryToUser(ActiveQuery $q):ActiveQuery {

        return $q
            ->addSelect([
                'plus' => 'plus.plus',
                'minus' => 'minus.minus',
                'balance' => '(COALESCE(plus.plus,0) - COALESCE(minus.minus,0))',
            ])
            ->leftJoin(
                [
                    'plus' => (new \yii\db\Query())
                        ->from((self::$class)::tableName())
                        ->select([
                            'to',
                            'plus' => 'SUM(amount)',
                        ])
                        ->where([
                            'deleted_at' => null
                        ])
                        ->groupBy('to')
                ],
                ($q->modelClass)::tableName().'.id=plus.to'
            )
            ->leftJoin(
                [
                    'minus' => (new \yii\db\Query())
                        ->from((self::$class)::tableName())
                        ->select([
                            'from',
                            'minus' => 'SUM(amount)',
                        ])
                        ->where([
                            'deleted_at' => null
                        ])
                        ->groupBy('from')
                ],
                ($q->modelClass)::tableName().'.id=minus.from'
            );

    }

}