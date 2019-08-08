<?php

namespace scl\yii\tools\components;

use scl\tools\rest\components\ErrorResponse;

/**
 * Class ApiErrorHandler
 * @package restapi\components
 */
class ApiErrorHandler extends ErrorHandler
{
    /**
     * @param \Error|\Exception $exception
     * @return array|ErrorResponse
     */
    protected function convertExceptionToArray($exception)
    {
        $errorResponse = new ErrorResponse($exception);
        if (YII_DEBUG && isset($exception->statusCode) && $exception->statusCode == 500) {
            $errorResponse->errorInfo = explode("\n", $exception->getTraceAsString());
        }
        return $errorResponse;
    }
}
