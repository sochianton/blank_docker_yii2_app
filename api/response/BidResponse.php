<?php

namespace api\response;

use OpenApi\Annotations as OA;
use yii\base\Model;

/**
 * Class BidResponse
 * @package api\response
 * @OA\Schema(schema="BidResponse2")
 */
class BidResponse extends Model
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
     * @var float
     * @OA\Property(type="integer")
     */
    public $price;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $object;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $completeAt;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $createdAt;
    /**
     * @var string
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
    public $categoryName;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $employeeComment;
    /**
     * @var array
     * @OA\Property(type="array", @OA\Items(type="object", ref="#/components/schemas/WorkEmployeeResponse"))
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
     * @OA\Property(type="array", @OA\Items(type="object", ref="#/components/schemas/EmployeeFileResponse"))
     */
    public $files;

    /**
     * BidResponse constructor.
     * @param array $bidDto
     */
    public function __construct(array $bid)
    {
        parent::__construct();

        foreach ($this->attributes as $attr=>$val){
            $this->$attr = isset($bid[$attr])?$bid[$attr]:null;
        }
    }
}
