<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser as P;
use Forecasta\Parser\ParserContext as CTX;
use Forecasta\Parser\Impl\Util\Cacheable;

/**
 * パーサに関する共通機能を集約したトレイトです
 * @author nkoseki
 *
 */
trait ParserTrait
{
    use Cacheable;

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

    /**
     * パーサヒストリエントリ
     * @var P\HistoryEntry
     */
    private $parserHistoryEntry = null;

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
    public function onSuccess(CTX $context, $alias = "")
    {
        if ($this->debugMode) {
            $className = get_class($this);
            applLog("Parser:onSuccess", "[Name:$this->name] of <$className>");
        }

        $this->parserHistoryEntry->leave();
    }

    /**
     * @param string $alias
     */
    public function onError(CTX $context, $alias = "")
    {
        if ($this->debugMode) {
            $className = get_class($this);
            applLog("Parser:onError", "[Name:$this->name] of <$className>");
        }

        $this->parserHistoryEntry->leave();
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

        $this->parserHistoryEntry->enter($this);
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

            $intermediate = $parsed;
            $intermediate = preg_replace("/\s+/", "", $intermediate);
            $intermediate = str_replace("\r\n", "", $intermediate);
            $intermediate = str_replace("\r", "", $intermediate);
            $intermediate = str_replace("\n", "", $intermediate);

            if(empty($intermediate)) {
                //$parsed = "<WhiteSpace>";
                //return "<{$this->getName()}>";
                return "";
            }


            if($parsed === "\"") {
                $parsed = '\"';
                //return "<{$this->getName()}>";
            }

            return "{\"{$nm}\" : \"{$parsed}\"}";
        } else {
            return $parsed;
        }
    }

    public function addAt(P\Parser $parser) {
        if($parser instanceof P\HasMoreChildren) {
            $parser->add($this);
        }
        return $this;
    }

    public function getHistory() {
        return $this->parserHistoryEntry;
    }

    public function setHistory($history) {
        $this->parserHistoryEntry = $history;
    }


}

