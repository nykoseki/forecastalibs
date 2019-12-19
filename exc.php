<?php

require_once dirname(__FILE__). "/vendor/autoload.php";

// <editor-fold desc="旧コード">
$tst = new Forecasta\ForecastaMain();
/*
if(function_exists('Y')) {
    echo "Exists: Function Y\n";
}
if(function_exists('applLog')) {
    echo "Exists: Function applLog\n";
}

$res = $tst->parse001(). "";

$res = json_decode($res, true);

echo "Test". $tst->test0001(). "\n";
echo print_r($res, true). "\n";
*/


use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;

class TestInterceptor implements MethodInterceptor {
    public function invoke(MethodInvocation $invocation) {
        echo "pre\n";

        $result = $invocation->proceed();
        echo "post\n";

        return $result;
    }
}

/**
 * @Annotation
 * @Target("METHOD")
 * Class TestAnnotation
 */
class TestAnnotation {

}

use Ray\Aop\Pointcut;
use Ray\Aop\Matcher;
use Ray\Aop\Bind;
use Ray\Aop\Compiler;

/*
$pointcut = new Pointcut(
    (new Matcher)->any(),
    (new Matcher)->annotatedWith(TestAnnotation::class),
    [new TestInterceptor()]
);

class Te01 {

    public function met() {

        $clsName = get_class($this);
        $metName = __FUNCTION__;



        echo "{$clsName}#{$metName}\n";
        return "";
    }
}


$bind = (new Bind)->bind(Te01::class, [$pointcut]);
$tmpDir = __DIR__. "/tmp";
$compiler = new Compiler($tmpDir);

$obj = $compiler->newInstance(Te01::class, [], $bind);

$obj->met();
*/

class Te02 {

    /**
     * @aaa "aaaaa"
     * @Test(
     *     "aaa"=>"bbb",
     *     "ccc"=>"ddd"
     * )
     * @bbb("aaaaa" => "bbbbb", "ccccc" => "dddddd")
     * @return string
     */
    public function met() {

        $clsName = get_class($this);
        $metName = __FUNCTION__;

        echo "{$clsName}#{$metName}\n";
        return "";
    }

    /**
     * @aaa "aaaaa"
     * @Test(
     *     "aaa"=>"bbb",
     *     "ccc"=>"ddd"
     * )
     * @bbb("aaaaa" => "bbbbb", "ccccc" => "dddddd")
     * @xyz(
     *     "aaa" => "bbb",
     *     "ccc" => (
     *          "ddd" => "eee",
     *          "fff" => "gggg"
     *      )
     * )
     * @return string
     */
    public function met2() {

        $clsName = get_class($this);
        $metName = __FUNCTION__;

        echo "{$clsName}#{$metName}\n";
        return "";
    }
}

$t2 = new Te02();


$rc = new \ReflectionClass(get_class($t2));
$m = $rc->getMethod("met");

$comment = $m->getDocComment();

$ary = explode("\n", $comment);

$newAry = array();
foreach($ary as $value) {
    $ptn01 = '/\s*?\\/\\*\\*/i';
    $rep01 = '';
    $tmp = preg_replace($ptn01, $rep01, $value);

    $ptn02 = '/\s*?\\*\\//i';
    $rep02 = '';
    $tmp = preg_replace($ptn02, $rep02, $tmp);

    $ptn03 = '/\s*?\\*\s+?(.+)/i';
    $rep03 = '${1}';
    $tmp = preg_replace($ptn03, $rep03, $tmp);

    if(empty($tmp)) {
        continue;
    }

    array_push($newAry, $tmp);
    //echo $value. "\n";
}
// </editor-fold>
echo "=============================================================================================\n";

use Forecasta\Common\Test;

$f = Test::newInstance();

//$f->__()->__()->__()->__()->apply();

$f1 = $f->testMethod1;

$f1->__("param01")->__("param02")->rev()->__("param03")->rev()->__(function($param){
    return "[{$param}]";
})->ret();

$f1->clear();

$f1->__(function($param){
    return "[{$param}]";
})->__("ccc")->__("bbb")->__("aaa")->rev()->ret();

$f1->__(function($param){
    return "[{$param}]";
})->__("ccc")->__("bbb")->__("aaa")->back()->__("aaaa")->rev()->ret();

$func = function(){
    try {
        $data = yield 5;
        echo sprintf("%5s -> %s\n", 'Gen', $data);
        $data = yield 7;
        echo sprintf("%5s -> %s\n", 'Gen', $data);
        $data = yield 9;
        echo sprintf("%5s -> %s\n", 'Gen', $data);
    } catch(Exception $e) {


        echo sprintf("%5s -> %s\n", 'Gen', $e->getMessage());
        yield '!'. $e->getLine(). ':'. $e->getMessage(). '!';
    }

    yield;
};



$proc = $func();

echo sprintf("%5s -> %s\n", 'Main', $proc->current());
$proc->send(6);
echo sprintf("%5s -> %s\n", 'Main', $proc->current());
$proc->throw(new RuntimeException("Error!!!"));
$proc->send(8);
echo sprintf("%5s -> %s\n", 'Main', $proc->current());



echo "=============================================================================================\n";