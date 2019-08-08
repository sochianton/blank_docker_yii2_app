<?php

namespace api\modules\employee\v1\request;

use OpenApi\Annotations as OA;
use scl\yii\tools\Request;

/**
 * Class ProfileAddFcmTokenRequest
 * @package api\modules\customer\v1\request
 * @OA\Schema(schema="EmployeeProfileAddFcmTokenRequest", required={"token"})
 */
class ProfileAddFcmTokenRequest extends Request
{
    /**
     * @var string $token
     * @OA\Property(type="string")
     */
    public $token;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['token', 'required'],
            ['token', 'string'],
        ];
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}
