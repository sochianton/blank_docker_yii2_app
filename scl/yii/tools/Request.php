<?php

namespace scl\yii\tools;

use scl\tools\rest\interfaces\Request AS RequestInterface;
use yii\base\Model;

/**
 * Class Request
 * @package scl\tools\rest
 */
abstract class Request extends Model implements RequestInterface
{
    /**
     * Request constructor.
     * @param $request
     */
    public function __construct($request)
    {
        parent::__construct();

        $this->fillFromRequest($request);
    }

    /**
     * @param array $params
     * @return void
     * @internal param string $formName
     */
    public function fillFromRequest($params)
    {
        $this->load($params, '');
    }
}
