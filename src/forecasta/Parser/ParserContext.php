<?php

namespace Forecasta\Parser;

/**
 * パーサの入力と出力を司るコンテキストクラスです
 * @author nkoseki
 *
 */
class ParserContext
{

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
    private $ctx = null;

    /**
     * 現在の解析を意味するタグです
     * @var string
     */
    private $name = "";

    private $skipFlg = false;

    /**
     * 解析に使用されたパーサです
     * @var null
     */
    private $parser = null;

    /**
     * 親コンテキストです
     * @var null
     */
    private $parent = null;

    /**
     * 子コンテキストです
     * @var array
     */
    private $children = array();

    public function __construct($target, $position, $parsed, $ctx)
    {
        $this->target = $target;
        $this->currentPosition = $position;
        $this->parsed = $parsed;
        $this->ctx = $ctx;
    }

    public function getParent() {
        return $this->parent;
    }

    public function setParent(ParserContext $context) {
        return $this->parent;
    }

    public function add(ParserContext $context) {
        array_push($this->children, $context);
    }

    public function children() {
        return $this->children();
    }

    public function parsedBy() {
        return $this->parser;
    }

    public function setParsedBy($parser) {
        $this->parser = $parser;
    }

    public function setSkip($skipFlg)
    {
        $this->skipFlg = $skipFlg;
    }

    public function isSkip()
    {
        return $this->skipFlg;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public static function create($target)
    {
        return new ParserContext($target, 0, null, false);
    }

    public static function getBlank() {
        return new ParserContext("", 0, null, false);
    }

    public function target()
    {
        return $this->target;
    }
    public function updateTarget($target) {
        $this->target = $target;
    }

    public function current()
    {
        return $this->currentPosition;
    }

    public function updateCurrent($currentPosition) {
        $this->currentPosition = $currentPosition;
    }

    public function parsed()
    {
        return $this->parsed;
    }

    public function updateParsed($parsed) {
        $this->parsed = $parsed;
    }

    public function result()
    {
        return $this->ctx;
    }

    public function updateResult($result) {
        $this->ctx = $result;
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
        return (($this->ctx) && ($this->currentPosition == mb_strlen($this->target)));
    }

    public function __toString()
    {
        return $this->output();
    }

    private function output() {
        //echo "toString\n";
        $parsed0 = $this->parsed;

        // Y-Combinatorで再帰処理を行う
        // 配列->[...]
        // 文字列->"..."

        $currentName = $this->getName();

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
                                    //$tmpItem = "<Dq>";
                                    $tmpItem = "";
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
                                } else if($tmpItem === null) {
                                    $tmpItem = "";
                                } else if($tmpItem === "[") {
                                    $tmpItem = "<Al>";
                                } else if($tmpItem === "]") {
                                    $tmpItem = "<Ar>";
                                } else if($tmpItem === ":") {
                                    $tmpItem = "<Cl>";
                                } else if($tmpItem === "<WhiteSpace>") {
                                    $tmpItem = "";
                                }

                                if(mb_strlen($tmpItem) === 0) {
                                    //echo "Empty!!\n";
                                }
                                //return '"' . $tmpItem . '"';
                                return $tmpItem;
                            }

                        }, $x);

                        $immidiate = array();
                        foreach($parsed0 as $v) {
                            if(mb_strlen($v) > 0) {
                                array_push($immidiate, $v);
                            }
                        }
                        $parsed0 = $immidiate;

                        if(count($parsed0) == 0) {
                            return "";
                        }
                        if(count($parsed0) == 1) {
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

        $res = ($this->ctx) ? '"OK"' : '"NG"';

        $finish = (mb_strlen($this->target) == $this->currentPosition) ? '"OK"' : '"NG"';

        $targetLen = mb_strlen($this->target);

        return "{\"Target\":<{$this->target}>, \"Length\":\"{$targetLen}\", \"Current\":{$this->currentPosition}, \"Result\":{$res}, \"Finish\":{$finish}, \"Parsed\":{$parsed0}}";
    }
}
