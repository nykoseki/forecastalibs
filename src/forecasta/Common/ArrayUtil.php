<?php

namespace Forecasta\Common;

class ArrayUtil
{
    public function flatten_array($value, $key, &$array)
    {
        if (!is_array($value)) {
            array_push($array, $value);
        } else {
            array_walk($value, array('self', 'flatten_array', &$array));
        }
    }

    public static function flatten($arr, callable $filter = null, callable $predicate = null)
    {
        $flat_arr = array();

        if (!is_array($arr)) {
            return $flat_arr;
        }

        foreach ($arr as $value) {
            if(is_array($value)) {
                $flat_arr = array_merge($flat_arr, self::flatten($value, $filter, $predicate));
            } else {
                if($filter !== null) {
                    $value = $filter->__invoke($value);
                }

                if($predicate !== null && $predicate->__invoke($value)) {
                    array_push($flat_arr, $value);
                } else if($predicate === null) {
                    array_push($flat_arr, $value);
                }
            }
        }

        return $flat_arr;
    }
}