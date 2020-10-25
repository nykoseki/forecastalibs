<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser as P;
use Forecasta\Parser\ParserContext as CTX;

/**
 * パース結果集約用トレイトです
 * @author nkoseki
 *
 */
trait Aggregator
{

    private $name;

    private $description;

    private $runtime = array();

    private $debugMode = false;

    private $relation = null;

    /**
     * 引数に指定した文字列を，このパーサで解析します
     * @param string $target
     */
    public function invoke($target)
    {
        $param = CTX::create($target);

        return $this->parse($param);
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function onSuccess($alias = "")
    {
        if ($this->debugMode) {
            $className = get_class($this);
            Forecasta\Common\applLog("Parser:onSuccess", "[Name:$this->name] of <$className>");
        }

    }

    public function onError($alias = "")
    {
        if ($this->debugMode) {
            $className = get_class($this);
            Forecasta\Common\applLog("Parser:onError", "[Name:$this->name] of <$className>");
        }

    }

    public function onTry($alias = "")
    {
        if ($this->debugMode) {
            $className = get_class($this);
            Forecasta\Common\applLog("Parser:onTry", "[Name:$this->name] of <$className>");
        }

    }


    public function decolateParsed($parsed) {
        if(!empty($this->getName())) {
            $nm = $this->getName();
            $clsName = get_class($this);

            return "<{$nm}> => \"{$parsed}\"";
        } else {
            return $parsed;
        }
    }
}

?>