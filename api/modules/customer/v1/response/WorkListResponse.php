<?php

namespace api\modules\customer\v1\response;

use common\dto\WorkDto;
use OpenApi\Annotations as OA;

/**
 * Class WorkListResponse
 * @package api\modules\customer\v1\response
 * @OA\Schema(schema="WorkListResponse")
 */
class WorkListResponse
{
    /**
     * @var array
     * @OA\Property(type="array", @OA\Items(type="object", ref="#/components/schemas/WorkCustomerResponse"))
     */
    public $works;

    /**
     * WorkListResponse constructor.
     * @param array $bids
     */
    public function __construct(array $bids)
    {
        $this->works = array_map(function (WorkDto $workDto) {
            return new WorkResponse($workDto);
        }, $bids);
    }
}
