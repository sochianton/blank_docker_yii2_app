<?php

namespace common\dto;

/**
 * Class WorkDto
 * @package common\dto
 */
final class WorkDto
{
    /**
     * @var integer $id
     */
    private $id;
    /**
     * @var string $name
     */
    private $name;
    /**
     * @var integer $price
     */
    private $price;
    /**
     * @var integer $commission
     */
    private $commission;
    /**
     * @var array $qualificationIds
     */
    private $qualificationIds;
    /**
     * @var integer $deletedAt
     */
    private $deletedAt;

    /**
     * WorkDto constructor.
     * @param array|null $params
     */
    public function __construct(?array $params = [])
    {
        $this->id = (int)($params['id'] ?? 0);
        $this->name = (string)($params['name'] ?? '');
        $this->price = ($params['price'] ?? 0);
        $this->commission = (int)($params['commission'] ?? 0);
        $this->qualificationIds = (array)($params['qualificationIds'] ?? []);
        $this->deletedAt = $params['deleted_at'] ?? null;
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
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice(float $price)
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getCommission(): int
    {
        return $this->commission;
    }

    /**
     * @param int $commission
     */
    public function setCommission(int $commission)
    {
        $this->commission = $commission;
    }

    /**
     * @return array
     */
    public function getQualificationIds(): array
    {
        return $this->qualificationIds;
    }

    /**
     * @param array $qualificationIds
     */
    public function setQualificationIds(array $qualificationIds)
    {
        $this->qualificationIds = $qualificationIds;
    }

    /**
     * @return int|null
     */
    public function getDeletedAt(): ?int
    {
        return $this->deletedAt;
    }

    /**
     * @param int|null $deletedAt
     */
    public function setDeletedAt(?int $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
