<?php

namespace Forecasta\Parser;

use Forecasta\Common\ArrayUtil;
use Forecasta\Common\Composite;
use Forecasta\Common\Named;
//use Forecasta\Common\Y;

/**
 * パーサの入力と出力を司るコンテキストクラスです
 * @author nkoseki
 *
 */
class ParserContext implements \ArrayAccess, \IteratorAggregate, \Countable
{
    use Composite;
    use Named;

    // == ArrayAccess ==================================================================================================
    public function offsetGet($index) {
        return isset($this->children[$index]) ? $this->children[$index] : null;
    }
    public function offsetExists($index) {
        return isset($this->children[$index]);
    }
    public function offsetSet($index, $value) {
        if(is_null($index)) {
            $this->children[] = value;
        } else {
            $this->children[$index] = $value;
        }
    }
    public function offsetUnset($index) {
        unset($this->children[$index]);
    }
    // == IteratorAggregate ============================================================================================
    public function getIterator() {
        return new \ArrayIterator($this->children);
    }
    // =================================================================================================================

    // == Countable ====================================================================================================
    public function count() {
        return count($this->children);
    }
    // =================================================================================================================

    /**
     * 解析対象文字列です
     * @var string
     */
    private $target = "";

    /**
     * 現在の解析位置です
     * @var int
     */
    private $currentPosition = 0;

    /**
     * 解析後文字列です
     * @var null
     */
    private $parsed = null;

    /**
     * true:解析成功/false:解析失敗
     * @var null
     */
    private $result = null;

    /**
     * スキップフラグ
     * @var bool
     */
    private $skipFlg = false;

    /**
     * 解析に使用されたパーサです
     * @var null
     */
    private $parser = null;

    private $history = null;

    /**
     * 解析対象文字列、解析開始位置、解析後文字列、解析結果を指定してパーサコンテキストを生成します
     * ParserContext constructor.
     * @param $target 解析対象文字列
     * @param $position 解析開始位置
     * @param $parsed 解析後文字列
     * @param $result 解析結果(true / false)
     */
    public function __construct($target, $position, $parsed, $result)
    {
        $this->target = $target;
        $this->currentPosition = $position;
        $this->parsed = $parsed;
        $this->result = $result;
    }

    /**
     * @return ParserContext
     */
    public function copy() : ParserContext {
        $newCtx = new ParserContext($this->target, $this->currentPosition, $this->parsed, $this->result);

        $newCtx->setName($this->getName());
        //$newCtx->setParser($this->getParser());

        return $newCtx;
    }

    /**
     * @param Parser $parser
     */
    public function setParser(/*Parser */$parser) {
        $this->parser = $parser;
    }

    /**
     * @return Parser
     */
    public function getParser()/* : Parser*/ {
        return $this->parser;
    }

    public function setSkip($skipFlg)
    {
        $this->skipFlg = $skipFlg;
    }

    public function isSkip()
    {
        return $this->skipFlg;
    }

    public static function create($target)
    {
        return new ParserContext($target, 0, null, false);
    }

    public static function getBlank()
    {
        return new ParserContext("", 0, null, false);
    }

    public function target()
    {
        return $this->target;
    }

    public function updateTarget($target)
    {
        $this->target = $target;
    }

    public function current()
    {
        return $this->currentPosition;
    }

    public function len()
    {
        return mb_strlen($this->target);
    }

    public function updateCurrent($currentPosition)
    {
        $this->currentPosition = $currentPosition;
    }

    public function parsed($formatFlg=false)
    {
        $parsed = $this->parsed;

        $parsed = ArrayUtil::reduction($parsed);


        return $parsed;
    }

    public function updateParsed($parsed)
    {
        $this->parsed = $parsed;
    }

    public function result()
    {
        return $this->result;
    }

    public function updateResult($result)
    {
        $this->result = $result;
    }

    public function currentTarget()
    {
        $target0 = $this->target();
        $position = $this->current();
        $len = mb_strlen($target0);

        $tmpTarget = mb_substr($target0, $position, $len - $position);
        return $tmpTarget;
    }

    public function isFinished()
    {
        return (($this->result) && ($this->currentPosition == mb_strlen($this->target)));
    }

    public function toFlatString() {
        $parsed = $this->parsed;

        if(is_array($parsed)) {
            $parsed = "<ShowChildReference>";
        }

        //var_dump($parsed);

        $res = $this->result() ? "OK" : "NG";
        $target = $this->target();

        $lastPosition = "{$this->current()}";


        $prefix = "<Result:{$res}, LastPosition:{$lastPosition} Parsed:{$parsed} of Target:";
        $len = mb_strlen($prefix);
        $padding = str_repeat(" ", $len);
        $padding = $padding. str_repeat("+",  + $this->current()).  "^";


        $resultMessage = $prefix. "{$target}>";
        $resultMessage = $resultMessage. "\n". $padding;


        return $resultMessage;
    }

    public function __toString()
    {
        return $this->output();
    }

    private function output()
    {
        //echo "toString\n";
        $parsed0 = $this->parsed;



        // skipが付いたものを除外
        //array_filter($parsed0, function($item){});

        // Y-Combinatorで再帰処理を行う
        // 配列->[...]
        // 文字列->"..."

        $currentName = $this->getName();

        /*
        $parsed0 = Y(function ($callback) use (&$currentName) {
            return function ($x) use (&$callback, &$currentName) {
                if (is_array($x)) {
                    if (count($x) == 0) {
                        return '"<Null>"';
                    } else {

                        $parsed0 = array_map(function ($item) use (&$callback) {
                            if (is_array($item)) {
                                return $callback($item);
                            } else {
                                //return $item;
                                $tmpItem = $item;
                                if ($tmpItem === "\"") {
                                    $tmpItem = "<Dq>";
                                    //$tmpItem = "";
                                } else if ($tmpItem === " ") {
                                    //$tmpItem = "<Sp>";
                                    $tmpItem = "";
                                } else if ($tmpItem === "(") {
                                    $tmpItem = "<Lb>";
                                } else if ($tmpItem === ")") {
                                    $tmpItem = "<Rb>";
                                } else if ($tmpItem === "'") {
                                    $tmpItem = "<Sq>";
                                } else if ($tmpItem === ",") {
                                    //$tmpItem = "<Camma>";
                                    $tmpItem = "";
                                } else if (preg_match('/^\s+$/', $tmpItem) > 0) {
                                    //$tmpItem = "<Ws>";
                                    $tmpItem = "";
                                } else if ($tmpItem === "") {
                                    $tmpItem = "<Empty>";
                                    $tmpItem = "";
                                } else if ($tmpItem === "\t") {
                                    $tmpItem = "<Tab>";
                                } else if ($tmpItem === "{") {
                                    $tmpItem = "<wLb>";
                                } else if ($tmpItem === "}") {
                                    $tmpItem = "<wRb>";
                                } else if ($tmpItem === null) {
                                    $tmpItem = "";
                                } else if ($tmpItem === "[") {
                                    $tmpItem = "<Al>";
                                } else if ($tmpItem === "]") {
                                    $tmpItem = "<Ar>";
                                } else if ($tmpItem === ":") {
                                    $tmpItem = "<Cl>";
                                } else if ($tmpItem === "<WhiteSpace>") {
                                    $tmpItem = "";
                                } else if ($tmpItem === "=>") {
                                    $tmpItem = "<Arrow>";
                                }

                                if (mb_strlen($tmpItem) === 0) {
                                    //echo "Empty!!\n";
                                }
                                //return '"' . $tmpItem . '"';
                                return $tmpItem;
                            }

                        }, $x);

                        $immidiate = array();
                        foreach ($parsed0 as $v) {
                            if (mb_strlen($v) > 0) {
                                array_push($immidiate, $v);
                            }
                        }
                        $parsed0 = $immidiate;

                        if (count($parsed0) == 0) {
                            return "";
                        }
                        if (count($parsed0) == 1) {
                            return implode(', ', $parsed0);
                        }

                        $parsed0 = implode(', ', $parsed0);
                        $parsed0 = '[' . $parsed0 . ']';
                        return $parsed0;
                    }
                } else if (is_null($x)) {
                    return '"<Null>"';
                } else {
                    return '"' . $x . '"';
                }
            };
        })->__invoke($parsed0);
        */
        $parsed0 = $this->normalize($parsed0);

        $res = ($this->result) ? '"OK"' : '"NG"';

        $finish = (mb_strlen($this->target) == $this->currentPosition) ? '"OK"' : '"NG"';

        $targetLen = mb_strlen($this->target);

        return "{\"Target\":<{$this->target}>, \"Length\":\"{$targetLen}\", \"Current\":{$this->currentPosition}, \"Result\":{$res}, \"Finish\":{$finish}, \"Parsed\":{$parsed0}}";
    }

    public function normalize() {

        $currentName = $this->getName();

        $parsed = Y(function ($callback) use (&$currentName) {
            return function ($x) use (&$callback, &$currentName) {
                if (is_array($x)) {
                    if (count($x) == 0) {
                        return '"<Null>"';
                    } else {

                        $parsed0 = array_map(function ($item) use (&$callback) {
                            if (is_array($item)) {
                                return $callback($item);
                            } else {
                                //return $item;
                                $tmpItem = $item;
                                if ($tmpItem === "\"") {
                                    $tmpItem = "<Dq>";
                                    //$tmpItem = "";
                                } else if ($tmpItem === " ") {
                                    //$tmpItem = "<Sp>";
                                    $tmpItem = "";
                                } else if ($tmpItem === "(") {
                                    $tmpItem = "<Lb>";
                                } else if ($tmpItem === ")") {
                                    $tmpItem = "<Rb>";
                                } else if ($tmpItem === "'") {
                                    $tmpItem = "<Sq>";
                                } else if ($tmpItem === ",") {
                                    $tmpItem = "<Camma>";
                                    //$tmpItem = "";
                                } else if (preg_match('/^\s+$/', $tmpItem) > 0) {
                                    //$tmpItem = "<Ws>";
                                    $tmpItem = "";
                                } else if ($tmpItem === "") {
                                    $tmpItem = "<Empty>";
                                    $tmpItem = "";
                                } else if ($tmpItem === "\t") {
                                    $tmpItem = "<Tab>";
                                } else if ($tmpItem === "{") {
                                    $tmpItem = "<wLb>";
                                } else if ($tmpItem === "}") {
                                    $tmpItem = "<wRb>";
                                } else if ($tmpItem === null) {
                                    $tmpItem = "";
                                } else if ($tmpItem === "[") {
                                    $tmpItem = "<Al>";
                                } else if ($tmpItem === "]") {
                                    $tmpItem = "<Ar>";
                                } else if ($tmpItem === ":") {
                                    $tmpItem = "<Cl>";
                                } else if ($tmpItem === "<WhiteSpace>") {
                                    $tmpItem = "";
                                } else if ($tmpItem === "=>") {
                                    $tmpItem = "<Arrow>";
                                }

                                if (mb_strlen($tmpItem) === 0) {
                                    //echo "Empty!!\n";
                                }
                                //return '"' . $tmpItem . '"';
                                return $tmpItem;
                            }

                        }, $x);

                        $immidiate = array();
                        foreach ($parsed0 as $v) {
                            if (mb_strlen($v) > 0) {
                                array_push($immidiate, $v);
                            }
                        }
                        $parsed0 = $immidiate;

                        if (count($parsed0) == 0) {
                            return "";
                        }
                        if (count($parsed0) == 1) {
                            return implode(', ', $parsed0);
                        }

                        $parsed0 = implode(', ', $parsed0);
                        $parsed0 = '[' . $parsed0 . ']';
                        return $parsed0;
                    }
                } else if (is_null($x)) {
                    return '"<Null>"';
                } else {
                    return '"' . $x . '"';
                }
            };
        })->__invoke($this->parsed);

        return $parsed;
    }
}
