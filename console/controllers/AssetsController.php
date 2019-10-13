<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

class AssetsController extends Controller
{

    /**
     * Специальный контрол для сборки всех ассетов, необходимо корректное описание AppAsset
     * Следует обратить внимание на тот момент, в каком месте будет вызываться эта команда.
     * При генерации в качестве хешей используется стандартный алгоритм.
     * Нужно изменить его на поведение, при котором точно будет обеспечена генерация одинаковые хешей в названии папок.
     *
     * Для этого необходимо в common/config/main.php переопределить коллбэк функция assetManager-а:
     * 'assetManager' => [
     *   'hashCallback' => function ($path) {
     *           $path = str_replace(Yii::getAlias('@root'), '', $path);
     *           return substr(hash('md4', $path), 0, 8);
     *       }
     *  ],
     *
     * Для того, чтобы был доступен алиас root, нужно в common/config/bootstrap.php прописать:
     * Yii::setAlias('@root', dirname(dirname(__DIR__)));
     *
     * @return int
     */
    public function actionIndex()
    {
        // Создаем ассеты для раздачи
        Yii::setAlias('@webroot', __DIR__ . '/../../backend/web');
        Yii::setAlias('@web', '/');

        \backend\assets\BackendAsset::register($this->getView());

        echo 'Assets was created successfully.' . PHP_EOL;
        return 0;
    }
}
