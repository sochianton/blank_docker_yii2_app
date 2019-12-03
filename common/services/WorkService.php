<?php


namespace common\services;


use common\interfaces\BaseServiceInterface;
use \common\ar\Work;
use common\repositories\QualificationRep;
use common\repositories\WorkRep;
use common\traits\ServiceTrait;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class WorkService implements BaseServiceInterface
{

    use ServiceTrait;

    /** @var  WorkRep*/
    static $repository = WorkRep::class;

    static function getDto(Work $work): array{
        return $work->toArray([
            'id',
            'name',
            'price',
            'commission',
        ],[
            'deletedAt',
            'qualificationIds',
        ]);
    }

    /**
     * @param int $id
     * @return array
     */
    static function getQualificationIds(int $id): array
    {
        return (self::$repository)::getQualificationIds($id);
    }

    /**
     * @param int $id
     * @return Work[]
     */
    static function getByCategoryIds(int $id): ?array
    {
        return (self::$repository)::getWorksByCategoryId($id);
    }

    /**
     * @return array
     */
    static function getList(): array
    {
        return ArrayHelper::map((self::$repository)::getAll(), 'id', 'name');
    }

    /**
     * @return array
     */
    static function getIds(): array
    {
        return ArrayHelper::getColumn((self::$repository)::getAll(), 'id');
    }

    /**
     * @param array $names
     * @return array
     */
    static function getIdsByName(array $names): array
    {
        return ArrayHelper::getColumn((self::$repository)::getWorksByNames($names) , 'id');
    }

    /**
     * @return array
     */
    static function getNames(): array
    {
        return ArrayHelper::getColumn((self::$repository)::getAll(), 'name');
    }

    /**
     * @return array
     */
    static function getListCategoried(): array
    {
        return ArrayHelper::map((self::$repository)::getAllWithCats(), 'id', 'name', 'c_name');
    }

    /**
     * @param $model
     * @param bool $runValidation
     * @param null $attributeNames
     * @return \common\ar\Work
     * @throws \Throwable
     */
    public static function insert($model, bool $runValidation = true, $attributeNames = null) :Work
    {

        if(!$model->isNewRecord){
            throw new NotFoundHttpException(Yii::t('app', 'Model is not new'));
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {

            /** @var \common\ar\Work $work */
            (self::$repository)::insert($model);

            $qualifications=$model->qualifications;
            if(!is_array($qualifications)){
                $qualifications = array($model->qualifications);
            }

            if (!empty($qualifications)) {

                (self::$repository)::insertQualificationArray($model->id, $qualifications);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        }

        return $model;
    }

    /**
     * @param $model
     * @param bool $runValidation
     * @param null $attributeNames
     * @return Work
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    static function update($model, bool $runValidation = true, $attributeNames = null): Work
    {
        if($model->isNewRecord){
            throw new NotFoundHttpException(Yii::t('app', 'Model need to be not new'));
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            (self::$repository)::update($model);
            (self::$repository)::deleteQualificationArray([$model->id]);

            $qualifications=$model->qualifications;
            if(!is_array($qualifications)){
                $qualifications = array($model->qualifications);
            }

            if (!empty($qualifications)) {

                (self::$repository)::insertQualificationArray($model->id, $qualifications);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        }

        return $model;
    }

    /**
     * @param $params
     * @return array
     * @throws \Exception
     */
    static function searchFromApi($params){

        $work = new Work();

        $provider = $work->search($params);

        return array_map(function (Work $model){
            return self::getDto($model);
        }, $provider->getModels());

    }

    /**
     * @param int|null $category
     * @return array
     * @throws \Exception
     */
    static function getAllFromApi(int $category = null): array
    {
        if ($category) {
            $workIds = QualificationRep::getWorkIdsByQualifications([(int)$category]);
        }

        $work = new Work();

        $params=[];
        if(!empty($workIds)){
            $params[$work->formName()] = [
                'id' => $workIds,
            ];
        }




        return self::searchFromApi($params);

//        return WorkRep::getAllList($workIds ?? []);
    }

    /**
     * @param $id
     * @return array
     */
    static function getByBidId($id): array{
        return WorkRep::getWorksByBidId($id);
    }

}