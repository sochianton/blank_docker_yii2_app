<?php

namespace api\modules\employee\v1\response;

use OpenApi\Annotations as OA;

/**
 * Class LoginResponse
 * @package api\modules\employee\v1\response
 * @OA\Schema(schema="EmployeeLoginResponse")
 */
class LoginResponse
{
    /**
     * @var string $bearerToken
     * @OA\Property()
     */
    public $bearerToken;

    /**
     * LoginResponse constructor.
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->bearerToken = $token ?? null;
    }
}
