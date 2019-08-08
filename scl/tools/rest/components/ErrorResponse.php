<?php

namespace scl\tools\rest\components;

use scl\tools\rest\interfaces\Response;

/**
 * Class ErrorResponse
 * @package scl\tools\rest\components
 */
class ErrorResponse implements Response
{
    /** @var Error $error */
    public $error;
    /** @var mixed $errorInfo */
    public $errorInfo;

    /**
     * ErrorResponse constructor.
     * @param \Throwable $exception
     */
    public function __construct(\Throwable $exception)
    {
        $this->error = new Error($exception);
    }
}
