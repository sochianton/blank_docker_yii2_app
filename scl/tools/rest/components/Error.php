<?php

namespace scl\tools\rest\components;

/**
 * Class Error
 * @package restapi\modules\customer\v1\logic
 */
class Error
{
    /** @var string $message */
    public $message;

    /** @var int $code */
    public $code;

    public function __construct(\Throwable $exception)
    {
        $this->message = $exception->getMessage();
        $this->code = $exception->getCode() ?? 0;
    }
}
