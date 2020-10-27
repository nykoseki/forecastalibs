<?php

namespace Forecasta\Common;

trait ProxyTrait
{
    public function getProxy()
    {
        return new class($this)
        {
            private $_Forecasta_Proxy_ProxyTrait_Proxy;

            public function __construct($obj) {
                $this->_Forecasta_Proxy_ProxyTrait_Proxy = $obj;
            }

            public function __call($name, $arguments)
            {
                $reflMethod = new \ReflectionMethod(get_class($this->_Forecasta_Proxy_ProxyTrait_Proxy), $name);

                echo print_r($reflMethod, true). PHP_EOL;

                return $reflMethod->invokeArgs($this->_Forecasta_Proxy_ProxyTrait_Proxy, $arguments);

                //return call_user_func_array($this->_Forecasta_Proxy_ProxyTrait_Proxy->$name, $arguments);
            }
        };
    }
}