<?php

namespace api\modules\employee\v1\response;

use api\modules\employee\v1\dto\ProfileDto;
use OpenApi\Annotations as OA;

/**
 * Class ProfileResponse
 * @package api\modules\employee\v1\response
 * @OA\Schema(schema="EmployeeProfileResponse")
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
     * @var float
     * @OA\Property(type="number")
     */
    public $balance;
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
        $this->balance = $profileDto->balance;
        $this->fcmTokens = $profileDto->fcmTokens;
    }
}
