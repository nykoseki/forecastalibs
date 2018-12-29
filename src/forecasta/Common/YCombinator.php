<?php

/**
 * Yコンビネータです
 * @param callable $F
 */

if (!function_exists('Y')) {
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


if (!function_exists('applLog')) {
    /**
     * ログ出力を行います
     * @description ログ出力を行います
     * @param $category
     * @param $message
     * @param bool $debugFlg
     */
    function applLog($category, $message, $debugFlg = false)
    {

    }
}

