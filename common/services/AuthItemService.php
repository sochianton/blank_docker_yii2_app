<?php


namespace common\services;


use common\ar\AuthItemChild;
use common\ar\AuthItems;
use Yii;
use yii\web\NotFoundHttpException;

class AuthItemService
{


    /**
     * @param AuthItems $model
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public static function insert(AuthItems $model, bool $runValidation = true, $attributeNames = null) :bool
    {

        if(!$model->isNewRecord){
            throw new NotFoundHttpException(Yii::t('app', 'Model is not new'));
        }
        if($runValidation AND !$model->validate()){
            return false;
        }

        $res = false;

        $transaction = Yii::$app->db->beginTransaction();
        try {

            $res = $model->insert(false, $attributeNames);

            if($res){
                self::deleteAllChildrenArray([$model->name]);
                self::insertChildrenArray($model->name, $model->childrenForm);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        }

        return (bool)$res;
    }

    /**
     * @param AuthItems $model
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    static function update(AuthItems $model, bool $runValidation = true, $attributeNames = null): bool
    {
        if($model->isNewRecord){
            throw new NotFoundHttpException(Yii::t('app', 'Model need to be not new'));
        }
        if($runValidation AND !$model->validate()){
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {

            $model->update(false, $attributeNames);
            self::deleteAllChildrenArray([$model->name]);
            self::insertChildrenArray($model->name, $model->childrenForm);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        }

        return true;
    }

    static function insertChildrenArray(string $parent, array $children): void
    {
        $model = new AuthItemChild();
        try {
            $query = [];
            foreach ($children as $child) {
                $query[] = [
                    'parent' => $parent,
                    'cild' => $child,
                ];
            }

            Yii::$app->db->createCommand()->batchInsert(
                AuthItemChild::tableName(),
                $model->attributes(),
                $query
            )->execute();
        } catch (\Exception $exception) {
            throw new \Exception(Yii::t('errors', 'Can\'t create work qualifications'));
        }
    }

    static function deleteAllChildrenArray(array $parents){

        return AuthItemChild::deleteAll(['parent' => $parents]);

    }

}