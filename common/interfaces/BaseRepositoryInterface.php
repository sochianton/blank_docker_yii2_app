<?php


namespace common\interfaces;


interface BaseRepositoryInterface
{

    /**
     * @param int $id
     * @return mixed
     */
    static function get(int $id);

    static function insert($model, bool $runValidation = true, $attributeNames = null);

    static function update($model, bool $runValidation = true, $attributeNames = null);

}