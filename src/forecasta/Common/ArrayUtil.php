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

    /**
     * 引数に指定された多次元配列について縮約を行います
     * ・サイズ0の配列を除去します
     * ・サイズ1の配列に関して、対象の配列の0番目の値を取り出します
     *
     * 上記処理を、再帰的に行います
     * @param $array
     * @return mixed
     */
    public static function reduction($array) {
        $yF = Y(function($callback){
            return function($item) use(&$callback){
                if(is_array($item)) {

                    if(count($item) > 0) {
                        if(count($item) == 1) {
                            //echo "eeee\n";
                            $result = $callback($item[0]);

                            return $result;
                        } else {
                            $intermediate = [];

                            // 再帰
                            foreach($item as $key => $value) {

                                $result = null;
                                if(is_array($value)) {
                                    if(count($value) == 0) {
                                        //echo "dddd\n";
                                    } else {
                                        if(count($value) == 1) {
                                            //echo "cccc\n";
                                            $result = $callback($value[0]);
                                        } else {
                                            //echo "bbbb\n";
                                            $result = $callback($value);
                                        }
                                    }
                                } else {
                                    //echo "aaaa\n";
                                    $result = $value;
                                }

                                if($result != null) {

                                    array_push($intermediate, $result);
                                    //$intermediate[] = $result;
                                }
                            }

                            return $intermediate;
                        }
                    } else {
                        // サイズ0
                        return null;
                    }
                } else {
                    return $item;
                }
            };
        });



        return $yF($array);
    }



}