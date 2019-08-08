<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use function OpenApi\scan;

/**
 * Class SwaggerController
 *
 * @package console\controllers
 */
class SwaggerController extends Controller
{
    /**
     * Special control for swagger generate
     */
    public function actionIndex()
    {
        ini_set('memory_limit', '1000M');
        $openApi = scan(Yii::getAlias('@root'), ['exclude' => ['vendor', 'docker', 'console', 'scl']]);

        $dataYml = $openApi->toYaml();
        $fileName = Yii::getAlias('@api') . '/web/swagger.yml';
        file_put_contents($fileName, $dataYml);

        $dataJson = $openApi->toJson();
        $fileName = Yii::getAlias('@api') . '/web/swagger.json';
        file_put_contents($fileName, $dataJson);

        echo "Success!\n";
    }
}
