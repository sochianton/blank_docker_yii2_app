<?php

namespace api\modules\employee\v1\request;

use api\modules\employee\v1\dto\LoginDto;
use OpenApi\Annotations as OA;
use scl\yii\tools\Request;

/**
 * Class LoginRequest
 * @package api\modules\employee\v1\request
 * @OA\Schema(schema="EmployeeLoginRequest")
 */
class LoginRequest extends Request
{
    /**
     * @var string $email
     * @OA\Property(type="string"),
     */
    public $email;

    /**
     * @var string $password
     * @OA\Property(type="string", minimum=6),
     */
    public $password;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],
            [['email'], 'string'],
            [['email'], 'email'],
            [['password'], 'string', 'min' => 6],
        ];
    }

    /**
     * @return LoginDto
     */
    public function getDto(): LoginDto
    {
        return new LoginDto(
            (string)$this->email,
            (string)$this->password
        );
    }
}
