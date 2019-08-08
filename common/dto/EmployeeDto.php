<?php

namespace common\dto;

use yii\web\UploadedFile;

/**
 * Class EmployeeDto
 * @package common\dto
 */
class EmployeeDto
{
    /**
     * @var string $phone
     */
    public $phone;
    /**
     * @var string $email
     */
    public $email;
    /**
     * @var string $name
     */
    public $name;
    /**
     * @var string $firstName
     */
    public $firstName;
    /**
     * @var string $secondName
     */
    public $secondName;
    /**
     * @var string $lastName
     */
    public $lastName;
    /**
     * @var int $balance
     */
    public $balance;
    /**
     * @var string $password
     */
    public $password;
    /**
     * @var int $status
     */
    public $status;
    /**
     * @var int|null
     */
    public $companyId;
    /**
     * @var UploadedFile|null
     */
    public $photo;
    /**
     * @var array
     */
    private $qualificationIds;

    /**
     * EmployeeDto constructor.
     * @param string $phone
     * @param string $email
     * @param string $password
     * @param string $name
     * @param string $secondName
     * @param string $lastName
     * @param int $balance
     * @param int $status
     * @param int|null $companyId
     * @param UploadedFile|null $photo
     * @param array $qualificationIds
     */
    public function __construct(
        string $phone,
        string $email,
        string $password,
        string $name,
        string $secondName,
        string $lastName,
        int $balance,
        int $status,
        ?int $companyId,
        ?UploadedFile $photo,
        array $qualificationIds = []
    ) {
        $this->email = $email;
        $this->phone = $phone;
        $this->name = $name;
        $this->password = $password;
        $this->secondName = $secondName;
        $this->lastName = $lastName;
        $this->balance = $balance;
        $this->status = $status;
        $this->companyId = $companyId;
        $this->photo = $photo;
        $this->qualificationIds = $qualificationIds;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getSecondName(): string
    {
        return $this->secondName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return int|null
     */
    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    /**
     * @return UploadedFile|null
     */
    public function getPhoto(): ?UploadedFile
    {
        return $this->photo;
    }

    /**
     * @return array
     */
    public function getQualificationIds(): array
    {
        return $this->qualificationIds;
    }

    /**
     * @return int
     */
    public function getBalance(): int
    {
        return $this->balance;
    }
}
