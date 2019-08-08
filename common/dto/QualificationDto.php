<?php

namespace common\dto;

/**
 * Class QualificationDto
 * @package common\dto
 */
final class QualificationDto
{
    /** @var integer $id */
    public $id;
    /** @var string $name */
    public $name;
    /** @var integer $deletedAt */
    public $deletedAt;

    /**
     * QualificationDto constructor.
     * @param array|null $params
     */
    public function __construct(?array $params = [])
    {
        $this->id = (int)($params['id'] ?? 0);
        $this->name = (string)($params['name'] ?? '');
        $this->deletedAt = $params['deleted_at'] ?? null;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $time
     */
    public function setDeletedAt(?int $time)
    {
        $this->deletedAt = $time;
    }

    /**
     * @return int|null
     */
    public function getDeletedAt(): ?int
    {
        return $this->deletedAt;
    }
}
