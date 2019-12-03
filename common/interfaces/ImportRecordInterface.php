<?php


namespace common\interfaces;


interface ImportRecordInterface
{

    /**
     * @return null|array
     */
    static function importHeader();

    /**
     * @return array
     */
    static function importHeaderDescription();

    /**
     * @return array
     */
    static function importAttributeRules();

}