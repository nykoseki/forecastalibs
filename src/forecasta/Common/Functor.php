<?php

namespace Forecasta\Common;

trait Curry {
    public function __get($param) {

        if($this->reflector->hasMethod($param)) {
            $refMethod = $this->reflector->getMethod($param);

            $ref = new class($refMethod, $this) {

                private $methodRef = null;
                private $targetIns = null;

                private $currentArgs = array();

                public function __construct(\ReflectionMethod $methodRef, $targetInstance)
                {
                    $this->methodRef = $methodRef;
                    $this->targetIns = $targetInstance;
                }

                public function __invoke($a) {
                    $tgtParams = $this->methodRef->getParameters();
                    $tgtArgCount = count($tgtParams);
                    if($tgtArgCount == 0) {
                        return $this->methodRef->invokeArgs($this->targetIns, array());
                    }

                    $argCount = count($this->currentArgs);

                    if($argCount < $tgtArgCount) {
                        array_push($this->currentArgs, $a);
                        $argCount = count($this->currentArgs);

                        if($argCount == $tgtArgCount) {
                            return $this->methodRef->invokeArgs($this->targetIns, $this->currentArgs);
                        }
                    }

                    return clone($this);
                }

                public function clear() {
                    $this->currentArgs = array();
                }
            };

            return $ref;
        }

        return $this;
    }
}