<?php

namespace api\modules\customer\v1\response;

use api\modules\customer\v1\dto\ProfileDto;
use OpenApi\Annotations as OA;

/**
 * Class ProfileResponse
 * @package api\modules\customer\v1\response
 * @OA\Schema(schema="CustomerProfileResponse")
 */
class ProfileResponse
{
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $email;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $phone;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $name;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $secondName;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $lastName;
    /**
     * @var string|null
     * @OA\Property(type="string")
     */
    public $photo;
    /**
     * @var array
     * @OA\Property(
     *     type="array",
     *     @OA\Items(type="string")
     * )
     */
    public $fcmTokens;

    /**
     * ProfileResponse constructor.
     * @param ProfileDto $profileDto
     */
    public function __construct(ProfileDto $profileDto)
    {
        $this->email = $profileDto->email;
        $this->phone = $profileDto->phone;
        $this->name = $profileDto->name;
        $this->secondName = $profileDto->secondName;
        $this->lastName = $profileDto->lastName;
        $this->photo = $profileDto->photo;
        $this->fcmTokens = $profileDto->fcmTokens;
    }
}
