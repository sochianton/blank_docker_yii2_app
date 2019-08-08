<?php

namespace api\modules\employee\v1\request;

use OpenApi\Annotations as OA;
use scl\yii\tools\Request;

/**
 * Class BidApplyRequest
 * @package api\modules\employee\v1\request
 * @OA\Schema(schema="EmployeeBidApplyRequest")
 */
class BidApplyRequest extends Request
{
    /**
     * @var boolean $apply
     * @OA\Property(type="boolean"),
     */
    public $apply;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['apply'], 'boolean'],
            [['apply'], 'required'],
        ];
    }

    /**
     * @return bool
     */
    public function isApply(): bool
    {
        return $this->apply;
    }
}
