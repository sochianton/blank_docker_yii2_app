<?php

namespace api\response;

use OpenApi\Annotations as OA;
use yii\base\Model;

/**
 * Class WorkResponse
 * @package api\response
 * @OA\Schema(schema="WorkResponse")
 */
class WorkResponse extends Model
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
     * @param array $workDto
     */
    public function __construct(array $work)
    {
        parent::__construct();

        foreach ($this->attributes as $attr=>$val){
            $this->$attr = isset($work[$attr])?$work[$attr]:null;
        }
    }
}
