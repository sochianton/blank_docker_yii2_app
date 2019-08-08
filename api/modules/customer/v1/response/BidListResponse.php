<?php

namespace api\modules\customer\v1\response;

use common\dto\BidDto;
use OpenApi\Annotations as OA;

/**
 * Class BidListResponse
 * @package api\modules\customer\v1\response
 * @OA\Schema(schema="CustomerBidListResponse")
 */
class BidListResponse
{
    /**
     * @var array
     * @OA\Property(type="array", @OA\Items(type="object", ref="#/components/schemas/CustomerBidResponse"))
     */
    public $bids;

    /**
     * BidListResponse constructor.
     * @param BidDto[] $bids
     */
    public function __construct(array $bids)
    {
        $this->bids = array_map(function (BidDto $bidDto) {
            return new BidResponse($bidDto);
        }, $bids);
    }
}
