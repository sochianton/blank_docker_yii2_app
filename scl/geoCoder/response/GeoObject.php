<?php

namespace scl\geoCoder\response;


/**
 * Class TimeZoneResponse
 * @package scl\googleMapsApi\responses
 */
class GeoObject
{
    private $name;
    private $address;
    /** @var string[] $types */
    private $types;
    private $latitude;
    private $longitude;

    /**
     * GeoObject constructor.
     * @param string $name
     * @param null|string $address
     * @param array $types
     * @param $latitude
     * @param $longitude
     */
    public function __construct(string $name, ?string $address, array $types, $latitude, $longitude)
    {
        $this->name = $name;
        $this->address = $address;
        $this->types = $types;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
}
