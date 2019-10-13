<?php

namespace common\interfaces;

use yii\base\Model;
use yii\data\ActiveDataProvider;

interface CRUDControllerModelInterface
{

    /**
     * получаем Сервис модели
     * @return mixed
     */
    static function getService();

    /**
     * Поиск по таблице
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params);

    /**
     * Возвращает таблицу моедли
     * @param $dataProvider
     * @param Model $searchModel
     * @param array $params
     * @param null $configOnly
     * @return mixed
     */
    static function getGridWidget($dataProvider, Model $searchModel, $params=[], $configOnly=null);

    /**
     * Получаем основную форму модели
     * @param null $params
     * @return string
     */
    function getForm($params=null):string;

    /**
     * Скрипты, которые вызываются при отображении Формы
     * @param $params
     * @return string
     */
    public function formScripts($params=null): string ;

}