<?php

namespace scl\geoCoder\objects;

/**
 * Class Coordinate
 */
class Coordinate
{
    /** @var float $longitude */
    protected $longitude;
    /** @var float $latitude */
    protected $latitude;
    /** @var float $accuracy */
    protected $accuracy;

    /**
     * Coordinate constructor.
     * @param float $longitude
     * @param float $latitude
     * @param float $accuracy
     */
    public function __construct(float $longitude, float $latitude, float $accuracy=1)
    {
        $this->setLongitude($longitude);
        $this->setLatitude($latitude);
        $this->accuracy = $accuracy;
    }

    /**
     * @param float $longitude
     */
    protected function setLongitude(float $longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @param float $latitude
     */
    protected function setLatitude(float $latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @return int
     */
    public function getAccuracy(): int
    {
        return $this->accuracy;
    }

}
