<?php

namespace scl\tools\rest\interfaces;

/**
 * Interface Request
 * @package scl\tools\rest\interface
 */
interface Request
{

    /**
     * @param array $params
     * @return void
     */
    public function fillFromRequest($params);
}
