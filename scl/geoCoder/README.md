# geo-coder

## INSTALLATION

### Install via subtree

Include like subtree to scl/geoCoder directory of project

git subtree add --squash --prefix=scl/geoCoder git@gitlab.icerockdev.com:scl/scl-yii/geo-coder.git tag_name

Example: `git subtree add --squash --prefix=scl/geoCoder git@gitlab.icerockdev.com:scl/scl-yii/geo-coder.git master`


## DOCS

### 2gis
````
https://github.com/2gis/mapsapi/blob/master/CONTRIBUTING.md

http://catalog.api.2gis.ru/doc/2.0/geo/method/search-geo/geo-point-radius
http://catalog.api.2gis.ru/doc/2.0/geo/method/search-query/query
````

### Yandex
````
https://tech.yandex.com/maps/doc/geosearch/concepts/request-docpage/
````

### Google
````
https://developers.google.com/places/web-service/search#TextSearchRequests
https://developers.google.com/places/web-service/search#nearby-search-and-text-search-responses

https://developers.google.com/maps/faq#languagesupport
````

## EXAMPLES

### Reverse geocode
````php

$apiKey = 'your key';
$geocoder = new \scl\geoCoder\YandexGeoCoder($apiKey);

//$geocoder = new \scl\geoCoder\GISGeoCoder($apiKey);
//$geocoder = new \scl\geoCoder\GoogleGeoCoder($apiKey);

$result = $geocoder->search(new \scl\geoCoder\objects\Coordinate(82.921451, 55.028790, 50));

````

### Search (without paging)
````php
$apiKey = 'your key';

$geocoder = new \scl\geoCoder\YandexGeoCoder($apiKey);
//$geocoder = new \scl\geoCoder\GISGeoCoder($apiKey);
//$geocoder = new \scl\geoCoder\GoogleGeoCoder($apiKey);

$result = $geocoder->geocode('Мэрия новосибирска');

````

## TODO: 

more error information for API  

## Authors
- Alex Shvedov <alexeii.shvedov@gmail.com>
- Dmitry Veremeiko <d.veremeiko@gmail.com>
