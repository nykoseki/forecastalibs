<?php

/**
 * Yコンビネータです
 * @param callable $F
 */



if (!function_exists("Y")) {
    /**
     * Yコンビネータです
     * @param callable $F クロージャ
     * @param null $scope スコープオブジェクト
     * @return mixed Yコンビネータ
     */
    function Y(callable $F, $scope = null)
    {
        if (!is_null($scope)) {
            $F = \Closure::bind($F, $scope, get_class($scope));
        }

        return $F(
            function () use (&$F) {
                $result = call_user_func_array(Y($F), func_get_args());
                return $result;
            }
        );
    }
}


if (!function_exists("applLog2")) {
    /**
     * ログ出力を行います
     * @description ログ出力を行います
     * @param $category
     * @param $message
     * @param bool $debugFlg
     *
     * @return a
     */
    function applLog2($category, $message, $debugFlg = false)
    {
        if(is_array($message)) {
            $message = print_r($message, true);
            return print_r("{$category}:". $message, true). "\n";
        } else if(is_object($message)) {
            return print_r("{$category}:". $message, true). "\n";
        } else {
            return $message. "\n";
        }
    }
}

