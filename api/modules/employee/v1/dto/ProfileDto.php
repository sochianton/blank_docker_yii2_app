<?php

namespace api\modules\employee\v1\dto;

/**
 * Class ProfileDto
 * @package api\modules\employee\v1\dto
 */
class ProfileDto
{
    /**
     * @var string $email
     */
    public $email;
    /**
     * @var string $phone
     */
    public $phone;
    /**
     * @var string $name
     */
    public $name;
    /**
     * @var string $secondName
     */
    public $secondName;
    /**
     * @var string $lastName
     */
    public $lastName;
    /**
     * @var string|null $photo
     */
    public $photo;
    /**
     * @var float $balance
     */
    public $balance;
    /**
     * @var array $fcmTokens
     */
    public $fcmTokens;

    /**
     * ProfileDto constructor.
     * @param string $email
     * @param string $phone
     * @param string $name
     * @param string $secondName
     * @param string $lastName
     * @param string|null $photo
     * @param float $balance
     * @param array $fcmTokens
     */
    public function __construct(
        string $email,
        string $phone,
        string $name,
        string $secondName,
        string $lastName,
        ?string $photo,
        float $balance,
        array $fcmTokens = []
    ) {
        $this->email = $email;
        $this->phone = $phone;
        $this->name = $name;
        $this->secondName = $secondName;
        $this->lastName = $lastName;
        $this->photo = $photo;
        $this->balance = $balance;
        $this->fcmTokens = $fcmTokens;
    }
}
