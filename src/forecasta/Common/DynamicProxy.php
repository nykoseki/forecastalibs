<?php

namespace Forecasta\Common;

class DynamicProxy
{
    private $_Forecasta_Proxy_ProxyTrait_Proxy;

    public function __construct($obj) {
        $this->_Forecasta_Proxy_ProxyTrait_Proxy = $obj;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array($this->_Forecasta_Proxy_ProxyTrait_Proxy->$name, $arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        $args = func_get_args();

        $methodName = $args[0];
        $className = get_class(self::_Forecasta_Proxy_ProxyTrait_Proxy);

        $parameter = array();
        $idx = 0;
        for($idx = 0; $idx < count($args); $idx++) {
            if($idx > 0) {
                array_push($parameter, $args[$idx]);
            }
        }

        return call_user_func_array(array($className, $methodName), $parameter);
    }
}