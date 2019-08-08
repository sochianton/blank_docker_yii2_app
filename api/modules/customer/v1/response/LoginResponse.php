<?php

namespace api\modules\customer\v1\response;

use OpenApi\Annotations as OA;

/**
 * Class LoginResponse
 * @package api\modules\customer\v1\response
 * @OA\Schema(schema="CustomerLoginResponse")
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
