<?php

namespace Forecasta\Common;

/**
 * 配列操作関連ユーティリティです
 * Class ArrayUtil
 * @package Forecasta\Common
 */
class ArrayUtil
{

    /**
     * 配列を再帰的にフラットな１次元配列に焼き直します.
     * @param $arr 対象配列
     * @param callable|null $filter 要素フィルタ
     * @param callable|null $predicate 要素述語
     * @return array 変換後配列
     */
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