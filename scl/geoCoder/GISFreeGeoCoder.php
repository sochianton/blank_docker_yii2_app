<?php

namespace scl\geoCoder;

use InvalidArgumentException;
use scl\geoCoder\objects\Coordinate;
use scl\geoCoder\response\GeoObject;


/**
 * Class GISFreeGeoCoder
 * @package scl\geoCoder
 */
final class GISFreeGeoCoder extends AbstractGeoCoder
{
    /** @var string $url */
    private $url = 'https://catalog.api.2gis.ru/2.0/geo/search';
    /** @var array $hashData */
    private $hashData = [];

    /**
     * GISFreeGeoCoder constructor.
     * @param string $apiKey
     * @param array $hashData
     * @param string $lang
     * @param callable|null $cacheGet
     * @param callable|null $cacheSet
     * @param int $cacheDuration
     * @param int $apiTimeout
     * @param string|null $sid
     * @throws exceptions\ParamsException
     */
    public function __construct(
        string $apiKey,
        array $hashData,
        string $lang = 'ru_RU',
        ?callable $cacheGet = null,
        ?callable $cacheSet = null,
        int $cacheDuration = 86400,
        int $apiTimeout = 10,
        ?string $sid = null
    ) {
        parent::__construct($apiKey, $lang, $cacheGet, $cacheSet, $cacheDuration, $apiTimeout, $sid);
        $this->hashData = $hashData;
    }

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
        $params[$this->getHashFieldName()] = $this->getHash($this->getStringForHash($params));

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
        $params[$this->getHashFieldName()] = $this->getHash($this->getStringForHash($params));

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

    /**
     * @param array $params
     * @return string
     */
    private function getStringForHash(array $params): string
    {
        /** @var string $urlPath */
        $urlPath = parse_url($this->url, PHP_URL_PATH);
        /** @var string $queryString */
        $queryString = '';
        ksort($params);
        /** @var string $value */
        foreach ($params AS $value) {
            $queryString .= $value;
        }
        return $urlPath . $queryString;
    }

    /**
     * @param string $param
     * @return int
     */
    private function getHash(string $param): int
    {
        /** @var int $h */
        $h = $this->getDelta();
        /** @var string $t */
        $t = $param . $this->getTail();
        /** @var int $a */
        $a = $this->getDefaultValue();

        for ($r = 0; $r < strlen($t); ++$r) {
            $a = $a * $h + $this->JS_charCodeAt($t, $r);
            $a = $this->bitwiseShift($a);
        }

        return $this->bitwiseShift($a);
    }

    /**
     * @param $str
     * @param $index
     * @return int
     */
    private function JS_charCodeAt($str, $index): int
    {
        /** @var string $utf16 */
        $utf16 = mb_convert_encoding($str, 'UTF-16LE', 'UTF-8');

        return ord($utf16[$index*2]) + (ord($utf16[$index*2+1]) << 8);
    }

    /**
     * @param int $value
     * @return int
     */
    private function bitwiseShift(int $value): int
    {
        /** @var int $value */
        $value = abs($value >> 0);

        return bindec(substr(decbin($value), -32));
    }

    /**
     * @return string
     */
    private function getHashFieldName(): string
    {
        /** @var string $fieldName */
        $fieldName = (string)($this->hashData[3] ?? '');
        if (empty($fieldName)) {
            throw new InvalidArgumentException('Invalid hash field name');
        }
        return $fieldName;
    }

    /**
     * @return int
     */
    private function getDelta(): int
    {
        /** @var int $fieldDelta1 */
        $fieldDelta1 = (int)($this->hashData[1] ?? 0);
        /** @var int $fieldDelta2 */
        $fieldDelta2 = (int)($this->hashData[5] ?? 0);
        if (empty($fieldDelta1) || empty($fieldDelta2)) {
            throw new InvalidArgumentException('Invalid hash delta value');
        }

        return $fieldDelta1 + $fieldDelta2;
    }

    /**
     * @return int
     */
    private function getDefaultValue(): int
    {
        /** @var int $fieldValue1 */
        $fieldValue1 = (int)($this->hashData[2] ?? 0);
        /** @var int $fieldValue2 */
        $fieldValue2 = (int)($this->hashData[4] ?? 0);
        if (empty($fieldValue1) || empty($fieldValue2)) {
            throw new InvalidArgumentException('Invalid hash default value');
        }

        return $fieldValue1 + $fieldValue2;
    }

    /**
     * @return string
     */
    private function getTail(): string
    {
        /** @var string $fieldTail1 */
        $fieldTail1 = (string)($this->hashData[0] ?? '');
        /** @var string $fieldTail2 */
        $fieldTail2 = (string)($this->hashData[6] ?? '');
        if (empty($fieldTail1) || empty($fieldTail2)) {
            throw new InvalidArgumentException('Invalid hash tail value');
        }

        return $fieldTail1 . $fieldTail2;
    }
}
