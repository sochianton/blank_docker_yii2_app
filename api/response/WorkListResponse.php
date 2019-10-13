<?php

namespace api\response;

use OpenApi\Annotations as OA;

/**
 * Class WorkListResponse
 * @package api\response
 * @OA\Schema(schema="WorkListResponse2")
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
     * @param array $works
     */
    public function __construct(array $works)
    {
        $this->works = array_map(function($arr){
            return new WorkResponse($arr);
        }, $works);
    }
}
