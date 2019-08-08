<?php

namespace scl\geoCoder;

use scl\geoCoder\objects\Coordinate;
use scl\geoCoder\response\GeoObject;


/**
 * Class YandexGeoCoder
 */
final class YandexGeoCoder extends AbstractGeoCoder
{
    private $geocodeUrl = 'https://geocode-maps.yandex.ru/1.x/';
    private $placesUrl = 'https://search-maps.yandex.ru/v1/';

    /**
     * @param Coordinate $coordinate
     * @return string
     */
    protected function getRequestParams(Coordinate $coordinate): string
    {
        $radius = $this->convertToGrad($coordinate->getAccuracy());
        $params = http_build_query(
            [
                'geocode' => "{$coordinate->getLongitude()},{$coordinate->getLatitude()}",
                'apikey' => $this->apiKey,
                'sco' => 'longlat',
                'kind' => 'house',
                'rspn' => 1,
                'spn' => "{$radius},{$radius}",
                'format' => 'json',
                'lang' => $this->lang,
            ]
        );
        return $this->geocodeUrl . '?' . $params;
    }

    private function convertToGrad(int $radius): string
    {
        // примитивное приведение в градусы по экватору, умножаем на 2 тк в запросе дифф между мин и макс
        $equator = 40075696;
        return number_format($radius * 360 * 2 / $equator, 10, '.', '');
    }


    /**
     * @param $response
     * @return int
     */
    protected function getCountFound(array $response): int
    {
        return $response['response']['GeoObjectCollection']['metaDataProperty']['GeocoderResponseMetaData']['found'] ?? 0;
    }

    /**
     * @param $response
     * @return bool Always true, when no exceptions on send
     */
    protected function isValidResponse($response): bool
    {
        return true;
    }

    /**
     * Parse API response to GeoObject array
     * @param array $data
     * @return GeoObject[]
     */
    protected function parseSearch(array $data): array
    {
        $response = [];
        foreach ($data['response']['GeoObjectCollection']['featureMember'] as $row) {
            $coordinates = explode(' ', $row['GeoObject']['Point']['pos']);
            $latitude = $coordinates[1] ?? 0;
            $longitude = $coordinates[0] ?? 0;

            $response[] = new GeoObject(
                $row['GeoObject']['name'], // show shot address, no object info in API
                $row['GeoObject']['metaDataProperty']['GeocoderMetaData']['Address']['formatted'],
                [$row['GeoObject']['metaDataProperty']['GeocoderMetaData']['kind']],
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
            'ru_RU', // русский
            'uk_UA', // украинский
            'be_BY', // белорусский
            'en_RU', //  ответ на английском, российские особенности карты;
            'en_US', // — ответ на английском, американские особенности карты;
            'tr_TR', // — турецкий (только для карты Турции).
        ];
    }

    /**
     * @param string $address
     * @return string
     */
    protected function getGeocodeParams(string $address): string
    {
        // see https://tech.yandex.com/maps/doc/geosearch/concepts/request-docpage/
        $params = [
            'apiKey' => $this->apiKey,
            'text' => $address,
            'lang' => $this->lang,
            'type' => 'geo',
        ];
        return $this->placesUrl . '?' . http_build_query($params);
    }

    /**
     * Parse API response to GeoObject array
     * @param array $data
     * @return GeoObject[]
     */
    protected function parseGeocode(array $data): array
    {
        $response = [];
        return $response;
    }
}
