<?php

namespace api\modules\customer\v1\response;

use common\dto\TransactionDto;
use OpenApi\Annotations as OA;

/**
 * Class TransactionResponse
 * @package api\modules\customer\v1\response
 * @OA\Schema(schema="CustomerTransactionResponse")
 */
class TransactionResponse
{
    /**
     * @var int
     * @OA\Property(type="integer")
     */
    public $bidId;
    /**
     * @var int
     * @OA\Property(type="integer")
     */
    public $date;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $object;
    /**
     * @var int
     * @OA\Property(type="integer")
     */
    public $price;
    /**
     * @var int
     * @OA\Property(type="integer")
     */
    public $commission;

    /**
     * TransactionResponse constructor.
     * @param TransactionDto $transactionDto
     */
    public function __construct(TransactionDto $transactionDto)
    {
        $this->bidId = $transactionDto->getBidId();
        $this->date = $transactionDto->getDate();
        $this->object = $transactionDto->getObject();
        $this->price = $transactionDto->getPrice();
        $this->commission = $transactionDto->getCommission();
    }
}
