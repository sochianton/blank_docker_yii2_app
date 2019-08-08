<?php

namespace api\modules\customer\v1\response;

use common\dto\BidDto;
use common\dto\WorkDto;
use common\models\Work;
use OpenApi\Annotations as OA;

/**
 * Class BidResponse
 * @package api\modules\customer\v1\response
 * @OA\Schema(schema="CustomerBidResponse")
 */
class BidResponse
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
    public $customerId;
    /**
     * @var int
     * @OA\Property(type="integer")
     */
    public $employeeId;
    /**
     * @var int
     * @OA\Property(type="integer")
     */
    public $status;
    /**
     * @var int
     * @OA\Property(type="integer")
     */
    public $price;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $object;
    /**
     * @var int
     * @OA\Property(type="string")
     */
    public $completeAt;
    /**
     * @var int
     * @OA\Property(type="string")
     */
    public $createdAt;
    /**
     * @var int
     * @OA\Property(type="string")
     */
    public $updatedAt;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $customerComment;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $employeeComment;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $categoryName;
    /**
     * @var array
     * @OA\Property(type="array", @OA\Items(type="object", ref="#/components/schemas/WorkCustomerResponse"))
     */
    public $works;
    /**
     * @var array
     * @OA\Property(type="array", @OA\Items(type="object", ref="#/components/schemas/EmployeeFileResponse"))
     */
    public $customerPhotos;
    /**
     * @var array
     * @OA\Property(type="array", @OA\Items(type="object", ref="#/components/schemas/EmployeeFileResponse"))
     */
    public $employeePhotos;
    /**
     * @var array
     * @OA\Property(type="array", @OA\Items(type="object", ref="#/components/schemas/CustomerFileResponse"))
     */
    public $files;

    /**
     * BidResponse constructor.
     * @param BidDto $bidDto
     */
    public function __construct(BidDto $bidDto)
    {
        $this->id = $bidDto->getId();
        $this->name = $bidDto->getName();
        $this->customerId = $bidDto->getCustomerId();
        $this->employeeId = $bidDto->getEmployeeId();
        $this->status = $bidDto->getStatus();
        $this->price = $bidDto->getPrice();
        $this->object = $bidDto->getObject();
        $this->completeAt = (string)$bidDto->getCompleteAt();
        $this->createdAt = (string)$bidDto->getCreatedAt();
        $this->categoryName = (string)$bidDto->getQualificationName();
        $this->updatedAt = (string)$bidDto->getUpdatedAt();
        $this->customerComment = (string)$bidDto->getCustomerComment();
        $this->employeeComment = (string)$bidDto->getEmployeeComment();
        $this->works = array_map(function (Work $work) {
            return new WorkResponse(new WorkDto($work->toArray()));
        }, $bidDto->getWorks());
        $this->customerPhotos = array_map(function (array $photo) {
            return new FileResponse($photo);
        }, $bidDto->getCustomerPhotos());
        $this->employeePhotos = array_map(function (array $photo) {
            return new FileResponse($photo);
        }, $bidDto->getEmployeePhotos());
        $this->files = array_map(function (array $photo) {
            return new FileResponse($photo);
        }, $bidDto->getFiles());
    }
}
