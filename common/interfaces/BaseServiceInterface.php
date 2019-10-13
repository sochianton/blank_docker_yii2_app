<?php


namespace common\interfaces;


interface BaseServiceInterface
{

    static function get(int $id);

    static function insert($model, bool $runValidation = true, $attributeNames = null);

    static function update($model, bool $runValidation = true, $attributeNames = null);

}