<?php

namespace api\modules\employee\v1\request;

use common\models\Bid;
use OpenApi\Annotations as OA;
use scl\yii\tools\Request;

/**
 * Class BidSearchRequest
 * @package api\modules\employee\v1\request
 * @OA\Schema(schema="EmployeeBidSearchRequest")
 */
class BidSearchRequest extends Request
{
    /**
     * @var string $term
     * @OA\Property(type="string"),
     */
    public $term;
    /**
     * @var int|null
     * @OA\Property(type="integer"),
     */
    public $status;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['term'], 'required'],
            [['term'], 'string', 'min' => 3],
            ['status', 'in', 'range' => array_keys(Bid::STATUSES)],
        ];
    }

    /**
     * @return string
     */
    public function getTerm(): string
    {
        return $this->term;
    }

    /**
     * @return int
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }
}
