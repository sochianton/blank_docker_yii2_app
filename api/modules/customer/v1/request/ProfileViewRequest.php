<?php

namespace api\modules\customer\v1\request;

use scl\yii\tools\Request;

/**
 * Class ProfileViewRequest
 * @package api\modules\customer\v1\request
 * @OA\Schema(schema="CustomerProfileRequest")
 */
class ProfileViewRequest extends Request
{
    /**
     * @var string $customerId
     * @OA\Property(type="integer")
     */
    public $customerId;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['customerId', 'required'],
            ['customerId', 'integer'],
        ];
    }

    /**
     * @return string
     */
    public function getCustomerId(): string
    {
        return $this->customerId;
    }
}
