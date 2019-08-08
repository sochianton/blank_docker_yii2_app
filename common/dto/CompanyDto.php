<?php

namespace common\dto;

/**
 * Class LocationDto
 *
 * @package common\dto
 */
class CompanyDto
{
    /**
     * @var int $type
     */
    public $type;
    /**
     * @var int $status
     */
    public $status;
    /**
     * @var string $name
     */
    public $name;
    /**
     * @var string $address
     */
    public $address;
    /**
     * @var int
     */
    private $numberOfContract;

    /**
     * CustomerDto constructor.
     * @param int $type
     * @param int $status
     * @param string $name
     * @param string $address
     * @param int $numberOfContract
     */
    public function __construct(
        int $type,
        int $status,
        string $name,
        string $address,
        int $numberOfContract
    ) {
        $this->type = $type;
        $this->status = $status;
        $this->name = $name;
        $this->address = $address;
        $this->numberOfContract = $numberOfContract;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
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
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return int
     */
    public function getNumberOfContract(): int
    {
        return $this->numberOfContract;
    }
}
