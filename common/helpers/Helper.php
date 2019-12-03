<?php


namespace common\helpers;


/**
 * Class Helper
 * @package common\helpers
 */
class Helper
{

    /**
     * Превращаем массив с ul элемент
     * @param $data
     * @param array $map - Карта замены ключей
     * @param boolean $withoutKeys - невыводить ключи?
     * @return string
     */
    static function ArrayToUl($data, $map = array(), $withoutKeys=false){

        $out = '<ul>';
        foreach ($data as $key=>$val){
            $parent = false;
            if(is_array($val) AND !empty($val)){
                $val = self::ArrayToUl($val, $map, $withoutKeys);
                $parent = true;
            }
            elseif(is_array($val) AND empty($val)){
                continue;
            }
            else{
                if($val == '') continue;
            }

            if(is_int($key)) $out .= $val.'</br>';
            elseif($withoutKeys AND !$parent){
                $out .= '<li>'.$val.'</li>';
            }
            else{
                $label = '<b>'.(isset($map[$key]) ? $map[$key] : $key).': </b>';
                $out .= '<li>'.$label.$val.'</li>';
            }

        }

        return $out.'</ul>';

    }

}