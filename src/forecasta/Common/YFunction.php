<?php

namespace Forecasta\Common;

class YFunction {
    private static $instance = null;

    public static function create(callback $callback, $scope = null) {
        if(self::$instance === null) {
            self::$instance = new self();
        }
        return self::instance($callback, $scope);
    }

    private function __invoke(callback $callback, $scope = null) {
        if (!is_null($scope)) {
            $callback = \Closure::bind($callback, $scope, get_class($scope));
        }

        return $callback(
            function () use (&$callback) {
                $result = call_user_func_array(Y($callback), func_get_args());
                return $result;
            }
        );
    }
}