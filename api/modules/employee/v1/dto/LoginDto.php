<?php

namespace api\modules\employee\v1\dto;

/**
 * Class LoginDto
 * @package api\modules\employee\v1\dto
 */
class LoginDto
{
    /** @var string $email */
    public $email;

    /** @var string $password */
    public $password;

    /**
     * LoginDto constructor.
     * @param string $email
     * @param string $password
     */
    public function __construct(
        string $email,
        string $password
    ) {
        $this->email = $email;
        $this->password = $password;
    }
}
