<?php

namespace api\modules\customer\v1\request;

use OpenApi\Annotations as OA;
use scl\yii\tools\Request;

/**
 * Class BidApproveRequest
 * @package api\modules\customer\v1\request
 * @OA\Schema(schema="CustomerBidApproveRequest")
 */
class BidApproveRequest extends Request
{
    /**
     * @var boolean $approve
     * @OA\Property(type="boolean"),
     */
    public $approve;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['approve'], 'boolean'],
            [['approve'], 'required'],
        ];
    }

    /**
     * @return bool
     */
    public function isApprove(): bool
    {
        return $this->approve;
    }
}
