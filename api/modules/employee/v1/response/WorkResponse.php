<?php

namespace api\modules\employee\v1\response;

use common\dto\WorkDto;
use OpenApi\Annotations as OA;

/**
 * Class WorkResponse
 * @package api\modules\employee\v1\response
 * @OA\Schema(schema="WorkEmployeeResponse")
 */
class WorkResponse
{
    /**
     * @var int
     * @OA\Property(type="integer")
     */
    public $id;
    /**
     * @var int
     * @OA\Property(type="string")
     */
    public $name;
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
     * @var array
     * @OA\Property(type="array", @OA\Items(type="integer"))
     */
    public $qualificationIds;
    /**
     * @var int
     * @OA\Property(type="integer")
     */
    public $deletedAt;

    /**
     * BidResponse constructor.
     * @param WorkDto $workDto
     */
    public function __construct(WorkDto $workDto)
    {
        $this->id = $workDto->getId();
        $this->name = $workDto->getName();
        $this->price = $workDto->getPrice();
        $this->commission = $workDto->getCommission();
        $this->qualificationIds = $workDto->getQualificationIds();
        $this->deletedAt = $workDto->getDeletedAt();
    }
}
