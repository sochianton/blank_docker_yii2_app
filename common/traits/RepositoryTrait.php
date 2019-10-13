<?php

namespace common\traits;

use common\ar\AppActiveRecord;
use Yii;
use yii\web\NotFoundHttpException;

trait RepositoryTrait
{

    /**
     * @param int $id
     * @return AppActiveRecord|null
     * @throws NotFoundHttpException
     */
    static function get(int $id){

        /** @var AppActiveRecord $class */
        $class = self::$class;
        $item = $class::findOne($id);
        if ($item === null) {
            throw new NotFoundHttpException(Yii::t('error', 'Record doesn\'t find'));
        }

        return $item;
    }

    /**
     * @param AppActiveRecord $model
     * @param bool $runValidation
     * @param array $attributeNames
     * @return AppActiveRecord
     * @throws \Throwable
     */
    static function insert($model, bool $runValidation = true, $attributeNames = null){

        if (!$model->insert($runValidation, $attributeNames)) {
            throw new \Exception(Yii::t('errors', 'Can\'t perform operation'));
        }
        return $model;
    }

    /**
     * @param $model AppActiveRecord
     * @param bool $runValidation
     * @param array $attributeNames
     * @return null| AppActiveRecord
     * @throws \Throwable
     */
    static function update($model, bool $runValidation = true, $attributeNames = null){

        //$model->save($runValidation);
        return $model->update($runValidation, $attributeNames) !== false ? $model : null;

    }

}