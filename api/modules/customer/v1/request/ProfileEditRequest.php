<?php

namespace api\modules\customer\v1\request;

use scl\yii\tools\Request;

/**
 * Class ProfileEditRequest
 * @package api\modules\customer\v1\request
 * @OA\Schema(schema="CustomerProfileEditRequest")
 */
class ProfileEditRequest extends Request
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
     * @return array
     */
    public function rules(): array
    {
        return [
            [
                [
                    'email',
                    'name',
                    'secondName',
                    'lastName',
                ],
                'required'
            ],
            [['email', 'name', 'secondName', 'lastName'], 'string'],
            ['email', 'email']
        ];
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone ?? '';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSecondName(): string
    {
        return $this->secondName ?? '';
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }
}
