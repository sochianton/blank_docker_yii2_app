<?php

namespace api\response;

use OpenApi\Annotations as OA;

/**
 * Class BidListResponse
 * @package api\response
 * @OA\Schema(schema="EmployeeBidListResponse2")
 */
class BidListResponse
{
    /**
     * @var array
     * @OA\Property(type="array", @OA\Items(type="object", ref="#/components/schemas/EmployeeBidResponse2"))
     */
    public $bids;

    /**
     * BidListResponse constructor.
     * @param BidResponse[] $bids
     */
    public function __construct(array $bids)
    {
        $this->bids = array_map(function($bidArr){
            return new \api\response\BidResponse($bidArr);
        }, $bids);
    }
}
