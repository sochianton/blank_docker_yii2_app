<?php

namespace common\dto;

/**
 * Class Transaction
 * @package common\dto
 */
class TransactionDto
{
    /**
     * @var int
     */
    public $bidId;
    /**
     * @var int
     */
    public $date;
    /**
     * @var string
     */
    public $customer;
    /**
     * @var string
     */
    public $employee;
    /**
     * @var string
     */
    public $object;
    /**
     * @var int
     */
    public $price;
    /**
     * @var int
     */
    public $commission;

    /**
     * TransactionDto constructor.
     * @param int $bidId
     * @param int $date
     * @param string $customer
     * @param string $employee
     * @param string $object
     * @param int $price
     * @param int $commission
     */
    public function __construct(
        int $bidId,
        int $date,
        string $customer,
        string $employee,
        string $object,
        int $price,
        int $commission
    ) {
        $this->bidId = $bidId;
        $this->date = $date;
        $this->customer = $customer;
        $this->employee = $employee;
        $this->object = $object;
        $this->price = $price;
        $this->commission = $commission;
    }

    /**
     * @return int
     */
    public function getBidId(): int
    {
        return $this->bidId;
    }

    /**
     * @return int
     */
    public function getDate(): int
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getCustomer(): string
    {
        return $this->customer;
    }

    /**
     * @return string
     */
    public function getEmployee(): string
    {
        return $this->employee;
    }

    /**
     * @return string
     */
    public function getObject(): string
    {
        return $this->object;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getCommission(): int
    {
        return $this->commission;
    }
}
