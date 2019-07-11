<?php

namespace Forecasta\Common;

class Test
{
    use Curry;

    private $reflector = null;

    private function __construct() {
        $this->reflector = new \ReflectionClass ($this);
    }

    public static function newInstance() {
        return new self();
    }

    public function testMethod1($a, $b, $c, \Closure $f) {

        $result = $f("{$a}-{$b}-{$c}");

        echo "call testMethod1-{$result}". PHP_EOL;

        return $this;
    }

    public function testMethod2() {
        echo "call testMethod2". PHP_EOL;

        return $this;
    }

    public function testMethod3($message) {
        echo "call testMethod2({$message})". PHP_EOL;

        return $this;
    }


}