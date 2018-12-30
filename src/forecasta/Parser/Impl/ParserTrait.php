<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser as P;
use Forecasta\Parser\ParserContext as CTX;

/**
 * パーサに関する共通機能を集約したトレイトです
 * @author nkoseki
 *
 */
trait ParserTrait
{
    /**
     * パーサ名です
     * @var
     */
    private $name;

    /**
     * パーサの説明文です
     * @var
     */
    private $description;

    private $runtime = array();

    /**
     * パーサの解析状態を詳細に出力する場合はtrueを設定します
     * @var bool
     */
    private $debugMode = false;

    /**
     * このパーサが解析した出力をスキップする場合はtrueを設定します
     * @var bool
     */
    private $skipFlg = false;

    /**
     * パーサの論理構文木を作成するために、内部的に使われるタグです
     * @var string
     */
    private $tag = "";

    public function isDebugMode()
    {
        return $this->debugMode;
    }

    public function setDebug($debugMode)
    {
        $this->debugMode = $debugMode;

        return $this;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    public function isSkip()
    {
        return $this->skipFlg;
    }

    public function skip($skipFlg)
    {
        $this->skipFlg = $skipFlg;

        return $this;
    }

    /**
     * 引数に指定した文字列を，このパーサで解析します
     * @param $target
     * @return ParserContext
     */
    public function invoke($target)
    {
        $param = CTX::create($target);

        return $this->parse($param);
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $alias
     */
    public function onSuccess($alias = "")
    {
        if ($this->debugMode) {
            $className = get_class($this);
            applLog("Parser:onSuccess", "[Name:$this->name] of <$className>");
        }
    }

    /**
     * @param string $alias
     */
    public function onError($alias = "")
    {
        if ($this->debugMode) {
            $className = get_class($this);
            applLog("Parser:onError", "[Name:$this->name] of <$className>");
        }

    }

    /**
     * @param string $alias
     */
    public function onTry($alias = "")
    {
        if ($this->debugMode) {
            $className = get_class($this);
            applLog("Parser:onTry", "[Name:$this->name] of <$className>");
        }

    }

    /**
     * パーサの出力結果を修飾します
     * @param $parsed
     * @return string
     */
    public function decolateParsed($parsed)
    {
        if (!empty($this->getName())) {
            $nm = $this->getName();
            $clsName = get_class($this);

            return "<{$nm}@\"{$parsed}\">";
        } else {
            return $parsed;
        }
    }
}

