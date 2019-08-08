<?php

namespace scl\geoCoder;

use scl\geoCoder\exceptions\ParamsException;
use scl\geoCoder\exceptions\ResponseException;
use scl\geoCoder\objects\Coordinate;
use scl\geoCoder\response\GeoObject;

/**
 * Class AbstractGeoCoder
 */
/*
 * TODO:
 * - поиск координат по названию
 * - единый интерфейс запроса
 * - автокомплит
 * - поиск с постраничкой
 * - постраничный вывод
 * - передача доп параметров в версию
 * - поиск в радиусе
 */

abstract class AbstractGeoCoder
{
    /** @var int $cacheDuration */
    protected $cacheDuration;
    /** @var callable $cacheGet */
    protected $cacheGet;
    /** @var callable $cacheSet */
    protected $cacheSet;
    /** @var string $apiKey */
    protected $apiKey;
    /** @var int $apiTimeout */
    protected $apiTimeout;
    /** @var string $lang */
    protected $lang;
    /** @var string|null $sid */
    protected $sid;

    /**
     * AbstractGeoCoder constructor.
     * @param string $apiKey
     * @param string $lang
     * @param callable $cacheGet
     * @param callable $cacheSet
     * @param int $cacheDuration
     * @param int $apiTimeout
     * @param null|string $sid
     * @throws ParamsException
     */
    public function __construct(
        string $apiKey,
        string $lang = 'ru_RU',
        ?callable $cacheGet = null,
        ?callable $cacheSet = null,
        int $cacheDuration = 86400,
        int $apiTimeout = 10,
        ?string $sid = null
    ) {
        $this->apiKey = $apiKey;
        $this->cacheGet = $cacheGet;
        $this->cacheSet = $cacheSet;
        $this->cacheDuration = $cacheDuration;
        $this->apiTimeout = $apiTimeout;
        $this->sid = $sid;

        if (!in_array($lang, $this->getSupportedLang())) {
            throw new ParamsException('Unsupported language');
        }
        $this->lang = $lang;
    }

    /**
     * @param Coordinate $coordinate
     * @return GeoObject[]
     * @throws ResponseException
     */
    public function search(Coordinate $coordinate): array
    {
        $url = $this->getRequestParams($coordinate);
        $result = $this->getForCache($url);

        if ($result === null) {
            $result = $this->send($url);
            if (!$this->isValidResponse($result)) {
                throw new ResponseException('Geocoder bad response');
            }

            $result = $this->parseSearch($result);
            $this->saveForCache($url, $result, $this->cacheDuration);
        }

        return $result;
    }

    /**
     * @param Coordinate $coordinate
     * @return string
     */
    protected abstract function getRequestParams(Coordinate $coordinate): string;

    /**
     * Parse API response to GeoObject array
     * @param array $data
     * @return GeoObject[]
     */
    protected abstract function parseSearch(array $data): array;

    /**
     * @param string $address
     * @return GeoObject[]
     * @throws ParamsException
     * @throws ResponseException
     */
    public function geocode(string $address)
    {
        if (empty($address)) {
            throw new ParamsException('Invalid geocode params');
        }

        // TODO: check about cache need, now not use
        $paramsStr = $this->getGeocodeParams($address);
        $result = $this->send($paramsStr);
        // TODO: check valid
        $result = $this->parseGeocode($result);
        return $result;
    }

    /**
     * @param string $address
     * @return string
     */
    protected abstract function getGeocodeParams(string $address): string;

    /**
     * Parse API response to GeoObject array
     * @param array $data
     * @return GeoObject[]
     */
    protected abstract function parseGeocode(array $data): array;

    /**
     * @param string $url
     * @return array
     * @throws ResponseException
     */
    protected function send(string $url): array
    {

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => false,
            CURLOPT_TIMEOUT => $this->apiTimeout,
//            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $response = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpStatus !== 200) {
            throw new ResponseException('Geocode API bad response');
        }

        return json_decode($response, true);
    }

    /**
     * @param $response
     * @return bool
     */
    protected abstract function isValidResponse($response): bool;


    /**
     * @param Coordinate $coordinate
     * @return bool
     * @throws ResponseException
     */
    public function checkBuilding(Coordinate $coordinate)
    {
        $params = $this->getRequestParams($coordinate);
        $response = $this->send($params);

        if (!$this->isValidResponse($response)) {
            throw new ResponseException('Geocoder bad response');
        }

        return $this->getCountFound($response) > 0;
    }

    /**
     * @param array $response
     * @return int
     */
    protected abstract function getCountFound(array $response): int;


    // Cache

    /**
     * @param string $key
     * @return mixed
     */
    protected function getForCache(string $key)
    {
        if ($this->cacheGet === null) {
            return null;
        }
        return call_user_func($this->cacheGet, $this->getKey($key));
    }

    /**
     * @param string $params
     * @param $data
     * @param int $duration
     * @return bool
     */
    protected function saveForCache(string $params, $data, int $duration = 60)
    {
        if ($this->cacheSet === null) {
            return null;
        }
        return call_user_func($this->cacheSet, $this->getKey($params), $data, $duration);
    }

    /**
     * @param string $string
     * @return string
     */
    protected function getKey(string $string): string
    {
        return 'GeoCoder::' . md5($string);
    }

    // lang support

    /**
     * Return language list
     * @return string[]
     */
    protected abstract function getSupportedLang(): array;
}
