<?php

namespace scl\geoCoder;

use scl\geoCoder\objects\Coordinate;
use scl\geoCoder\response\GeoObject;


/**
 * Class GISGeoCoder
 * @package common\components\geo_coder
 */
final class GISGeoCoder extends AbstractGeoCoder
{
    /** @var string $url */
    private $url = 'https://catalog.api.2gis.ru/2.0/geo/search';

    /**
     * @param Coordinate $coordinate
     * @return string
     */
    protected function getRequestParams(Coordinate $coordinate): string
    {
        // @see http://catalog.api.2gis.ru/doc/2.0/geo/method/search-geo/geo-point-radius
        $params = [
            'radius' => $coordinate->getAccuracy(),
            'point' => "{$coordinate->getLongitude()},{$coordinate->getLatitude()}",
            'format' => 'json',
            'fields' => 'items.geometry.selection,items.full_address_name',
            'type' => 'building',
            'locale' => $this->lang,
            'stat[sid]' => $this->sid,
        ];
        $params['key'] = $this->apiKey;

        return $this->url . '?' . http_build_query($params);
    }

    /**
     * @param array $response
     * @return int
     */
    protected function getCountFound(array $response): int
    {
        return $response['result']['total'] ?? 0;
    }

    /**
     * @param $response
     * @return bool
     */
    protected function isValidResponse($response): bool
    {
        return intval($response['meta']['code'] ?? 0) === 200;
    }

    /**
     * Parse API response to GeoObject array
     * @param array $data
     * @return GeoObject[]
     */
    protected function parseSearch(array $data): array
    {
        $response = [];
        foreach ($data['result']['items'] as $row) {
            // ignore not buildings
            if (!isset($row['address_name'])) {
                continue;
            }
            $coordinates = trim($row['geometry']['selection'], 'POINT()');
            $coordinates = explode(' ', $coordinates);
            $latitude = $coordinates[1] ?? 0;
            $longitude = $coordinates[0] ?? 0;

            $response[] = new GeoObject(
                $row['name'],
                $row['full_address_name'],
                [$row['type']],
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
        return [
            'cs_CZ',
            'en_CY',
            'es_CL',
            'it_IT',
            'ru_RU',
            'ru_KZ',
            'ru_UA',
            'en_AE',
            'ar_AE',
            'ru_KG',
            'uk_UA',
        ];
    }

    /**
     * @param string $address
     * @return string
     */
    protected function getGeocodeParams(string $address): string
    {
        // @see http://catalog.api.2gis.ru/doc/2.0/geo/method/search-query/query
        $params = [
            'q' => $address,
            'format' => 'json',
            'fields' => 'items.geometry.selection,items.full_address_name',
            'type' => 'building',
            'locale' => $this->lang,
        ];
        $params['key'] = $this->apiKey;

        return $this->url . '?' . http_build_query($params);
    }

    /**
     * Parse API response to GeoObject array
     * @param array $data
     * @return GeoObject[]
     */
    protected function parseGeocode(array $data): array
    {
        return $this->parseSearch($data);
    }
}
