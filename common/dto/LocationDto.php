<?php

namespace common\dto;

/**
 * Class LocationDto
 *
 * @package common\dto
 */
final class LocationDto
{
    /** @var integer $id */
    public $id;
    /** @var string $name */
    public $name;
    /** @var string $address */
    public $address;
    /** @var int $addressId */
    public $addressId;
    /** @var string $city_name */
    public $extraData;
    /** @var string $owner */
    public $owner;
    /** @var string $status */
    public $status;
}