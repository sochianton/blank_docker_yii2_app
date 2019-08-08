<?php

namespace api\modules\customer\v1\response;

use common\dto\TransactionDto;
use OpenApi\Annotations as OA;

/**
 * Class TransactionListResponse
 * @package api\modules\customer\v1\response
 * @OA\Schema(schema="CustomerTransactionListResponse")
 */
class TransactionListResponse
{
    /**
     * @var array
     * @OA\Property(type="array", @OA\Items(type="object", ref="#/components/schemas/CustomerTransactionResponse"))
     */
    public $transactions;

    /**
     * TransactionListResponse constructor.
     * @param TransactionDto[] $transactions
     */
    public function __construct(array $transactions)
    {
        $this->transactions = array_map(function (TransactionDto $transactionDto) {
            return new TransactionResponse($transactionDto);
        }, $transactions);
    }
}
