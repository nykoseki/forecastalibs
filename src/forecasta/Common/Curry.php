<?php

namespace Forecasta\Common;

/**
 * 既存のクラスのメソッドに関して、部分適用機能を導入します。
 *
 * Trait Curry
 * @package Forecasta\Common
 */
trait Curry
{

    private $reflector = null;

    /**
     * インスタンスメソッドがプロパティアクセスされた場合
     * そのインスタンスメソッドをラップしたCurryオブジェクトを生成して返します。
     *
     * @description メソッドのCurry化
     * @param $param
     * @return $this|callable|__anonymous@483 Curryオブジェクト
     * @throws \ReflectionException
     */
    public function __get($param)
    {
        if($this->reflector == null) {
            $this->reflector = new \ReflectionClass ($this);
        }
        if ($this->reflector->hasMethod($param)) {
            $refMethod = $this->reflector->getMethod($param);

            /**
             * Curryオブジェクト
             */
            $ref = new class($refMethod, $this)
            {

                private $methodRef = null;
                private $targetIns = null;

                private $currentArgs = array();

                public function __construct(\ReflectionMethod $methodRef, $targetInstance)
                {
                    $this->methodRef = $methodRef;
                    $this->targetIns = $targetInstance;
                }

                /**
                 * Curryオブジェクトが関数呼び出しされた場合
                 * @return callable|mixed|__anonymous@767
                 */
                public function __invoke()
                {
                    $arg = func_get_args();

                    if (count($arg) == 0) {
                        $arg = null;
                    } else if (count($arg) == 1) {
                        $arg = $arg[0];
                    } else {
                        $arg = array_shift($arg);
                    }

                    return $this->__($arg);
                }

                /**
                 * この関数オブジェクトに、$argで与えた引数を部分適用します
                 * @param $arg
                 * @return |mixed
                 */
                public function __($arg)
                {

                    $tgtParams = $this->methodRef->getParameters();
                    $tgtArgCount = count($tgtParams);
                    if ($tgtArgCount == 0) {
                        return $this->methodRef->invokeArgs($this->targetIns, array());
                    }

                    $argCount = count($this->currentArgs);

                    if ($argCount < $tgtArgCount) {

                        array_push($this->currentArgs, $arg);
                        $argCount = count($this->currentArgs);

                        if ($argCount >= $tgtArgCount) {


                            return $this;
                        }
                    }

                    return $this;
                }

                /**
                 * Curryオブジェクトに対する部分適用が完全の場合
                 * 対象のメソッドを実行し値を返却します
                 * @return mixed
                 */
                public function ret()
                {
                    $tgtParams = $this->methodRef->getParameters();
                    $tgtArgCount = count($tgtParams);
                    $argCount = count($this->currentArgs);
                    if ($argCount < $tgtArgCount) {

                        return $this;
                    }

                    $result = $this->methodRef->invokeArgs($this->targetIns, $this->currentArgs);

                    $this->clear();

                    return $result;
                }

                /**
                 * 直前に適用された値を取り消します
                 * @return mixed
                 */
                public function back()
                {
                    array_pop($this->currentArgs);
                    return $this;
                }

                /**
                 * 部分適用されているパラメータを逆順にし適用しなおします
                 * @return mixed
                 */
                public function rev()
                {
                    $this->currentArgs = array_reverse($this->currentArgs);
                    return $this;
                }

                /**
                 * 部分適用をリセットします
                 * @return mixed
                 */
                public function clear()
                {
                    $this->currentArgs = array();
                    return $this;
                }

                /**
                 * このCurryオブジェクトが指し示すメソッドが所属するインスタンスを返します
                 * @return null
                 */
                public function getSource()
                {
                    return $this->targetIns;
                }
            };

            return $ref;
        }

        return $this;
    }
}