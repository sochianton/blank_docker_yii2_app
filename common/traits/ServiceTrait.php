<?php

namespace common\traits;

use common\ar\AppActiveRecord;
use Yii;
use yii\web\NotFoundHttpException;

trait ServiceTrait
{

    /**
     * @param int $id
     * @return AppActiveRecord|null
     * @throws NotFoundHttpException
     */
    public static function get(int $id){
        $reposiroty = self::$repository;
        return $reposiroty::get($id);
    }

    /**
     * @param AppActiveRecord $model
     * @param bool $runValidation
     * @param array $attributeNames
     * @return AppActiveRecord
     * @throws \Throwable
     */
    public static function insert($model, bool $runValidation = true, $attributeNames = null){

        $reposiroty = self::$repository;
        return $reposiroty::insert($model, $runValidation, $attributeNames);
    }

    /**
     * @param $model AppActiveRecord
     * @param bool $runValidation
     * @param array $attributeNames
     * @return null
     * @throws \Throwable
     */
    public static function update($model, bool $runValidation = true, $attributeNames = null){

        $reposiroty = self::$repository;
        return $reposiroty::update($model, $runValidation, $attributeNames);

    }

}