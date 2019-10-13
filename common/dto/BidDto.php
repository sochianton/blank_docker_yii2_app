<?php

namespace common\dto;

use yii\web\UploadedFile;

/**
 * Class BidDto
 * @package common\dto
 */
class BidDto
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $name;
    /**
     * @var int
     */
    public $customerId;
    /**
     * @var null|int
     */
    public $employeeId;
    /**
     * @var int
     */
    public $status;
    /**
     * @var int
     */
    public $price;
    /**
     * @var string
     */
    public $object;
    /**
     * @var string $customerComment
     */
    public $customerComment;
    /**
     * @var string $employeeComment
     */
    public $employeeComment;
    /**
     * @var int
     */
    public $completeAt;
    /**
     * @var int
     */
    public $createdAt;
    /**
     * @var int
     */
    private $updatedAt;
    /**
     * @var array
     */
    public $works;
    /**
     * @var array|UploadedFile[]
     */
    public $customerPhotos;
    /**
     * @var array|UploadedFile[]
     */
    public $files;
    /**
     * @var array|UploadedFile[]
     */
    public $employeePhotos;
    /**
     * @var string
     */
    public $qualificationName;

    /**
     * BidDto constructor.
     * @param int $id
     * @param string $name
     * @param int $customerId
     * @param null|int $employeeId
     * @param int $status
     * @param int $price
     * @param string $object
     * @param string $customerComment
     * @param string $employeeComment
     * @param int $completeAt
     * @param int $createdAt
     * @param int $updatedAt
     * @param array $works
     * @param array|UploadedFile[] $customerPhotos
     * @param array|UploadedFile[] $files
     * @param array|UploadedFile[] $employeePhotos
     * @param string $qualificationName
     */
    public function __construct(
        int $id,
        string $name,
        int $customerId,
        ?int $employeeId,
        int $status,
        int $price,
        string $object,
        ?string $customerComment,
        ?string $employeeComment,
        $completeAt,
        $createdAt,
        $updatedAt,
        array $works,
        array $customerPhotos = [],
        array $files = [],
        array $employeePhotos = [],
        string $qualificationName = ''
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->customerId = $customerId;
        $this->employeeId = $employeeId;
        $this->status = $status;
        $this->price = $price;
        $this->object = $object;
        $this->customerComment = $customerComment;
        $this->employeeComment = $employeeComment;
        $this->completeAt = $completeAt;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->works = $works;
        $this->customerPhotos = $customerPhotos;
        $this->files = $files;
        $this->employeePhotos = $employeePhotos;
        $this->qualificationName = $qualificationName;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * @return null|int
     */
    public function getEmployeeId(): ?int
    {
        return $this->employeeId;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getObject(): string
    {
        return $this->object;
    }

    /**
     * @return array
     */
    public function getWorks(): array
    {
        return $this->works;
    }

    /**
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getCompleteAt()
    {
        return $this->completeAt;
    }

    /**
     * @return array|UploadedFile[]
     */
    public function getCustomerPhotos(): array
    {
        return $this->customerPhotos;
    }

    /**
     * @return array|UploadedFile[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @return array|UploadedFile[]
     */
    public function getEmployeePhotos()
    {
        return $this->employeePhotos;
    }

    /**
     * @return string|null
     */
    public function getCustomerComment(): ?string
    {
        return $this->customerComment;
    }

    /**
     * @return string|null
     */
    public function getEmployeeComment(): ?string
    {
        return $this->employeeComment;
    }

    /**
     * @return int
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return string
     */
    public function getQualificationName(): string
    {
        return $this->qualificationName;
    }
}
