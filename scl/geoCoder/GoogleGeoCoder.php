<?php

namespace scl\geoCoder;

use scl\geoCoder\exceptions\ApiException;
use scl\geoCoder\objects\Coordinate;
use scl\geoCoder\response\GeoObject;

/**
 * Class GoogleGeoCoder
 * @package common\components
 */
final class GoogleGeoCoder extends AbstractGeoCoder
{
    /*
     * Add more lang @see https://developers.google.com/maps/faq#languagesupport
     */
    private $langMap = [
        'ru_RU' => 'ru',
        'en_US' => 'en',
    ];

    private $placesUrl = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json';
    private $geocodeUrl = 'https://maps.googleapis.com/maps/api/place/textsearch/json';


    /**
     * @param array $response
     * @return int
     */
    protected function getCountFound(array $response): int
    {
        return isset($response['results']) ? count($response['results']) : 0;
    }

    /**
     * @param Coordinate $coordinate
     * @return string
     */
    protected function getRequestParams(Coordinate $coordinate): string
    {
        $query = [
            'location' => "{$coordinate->getLatitude()},{$coordinate->getLongitude()}",
            'language' => $this->lang,
            'radius' => $coordinate->getAccuracy(),
            'key' => $this->apiKey,
            'type' => 'establishment',
            // see https://developers.google.com/places/web-service/supported_types#table1
            // TODO: support for extended params
        ];

        return $this->placesUrl . '?' . http_build_query($query);
    }

    /**
     * @param $response
     * @return bool
     */
    protected function isValidResponse($response): bool
    {
        return isset($response['status']) && $response['status'] == 'OK';
    }

    /**
     * Parse API response to GeoObject array
     * @param array $data
     * @return GeoObject[]
     */
    protected function parseSearch(array $data): array
    {
        $response = [];
        foreach ($data['results'] as $row) {
            // additional filter for check buildings
            if (!in_array('establishment', $row['types'])) {
                continue;
            }

            $latitude = $row['geometry']['location']['lat'];
            $longitude = $row['geometry']['location']['lng'];

            $response[] = new GeoObject(
                $row['name'],
                $row['vicinity'],
                $row['types'],
                $latitude,
                $longitude
            );
        }
        return $response;
    }

    /**
     * Return language list
     * @return string[]
     */
    protected function getSupportedLang(): array
    {
        return array_keys($this->langMap);
    }

    /**
     * @param string $address
     * @return string
     */
    protected function getGeocodeParams(string $address): string
    {
        $query = [
            'key' => $this->apiKey,
            'query' => $address,
            'language' => $this->lang,
        ];

        return $this->geocodeUrl . '?' . http_build_query($query);
    }

    /**
     * Parse API response to GeoObject array
     * @param array $data
     * @return GeoObject[]
     * @throws ApiException
     */
    protected function parseGeocode(array $data): array
    {
        if (!isset($data['status'])) {
            throw new ApiException('Invalid response');
        }

        if ($data['status'] === 'ZERO_RESULTS') {
            return [];
        }

        if ($data['status'] !== 'OK') {
            throw new ApiException('API error: ' . $data['status']);
        }

        $response = [];
        foreach ($data['results'] as $row) {
            $response[] = new GeoObject(
                $row['name'],
                $row['formatted_address'],
                $row['types'],
                $row['geometry']['location']['lat'],
                $row['geometry']['location']['lng']
            );
        }

        return $response;
    }
}
