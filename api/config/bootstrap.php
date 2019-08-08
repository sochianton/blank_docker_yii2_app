<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="euroservice", version="0.1.0")
 * @OA\Parameter(in="header", name="Authorization", required=true, @OA\Schema(type="string"))
 */

// append this!
Yii::setAlias('@root', dirname(dirname(__DIR__)));
Yii::setAlias('@storage', dirname(dirname(__DIR__)) . '/storage');
