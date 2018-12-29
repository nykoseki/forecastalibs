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

    private $name;

    private $description;

    private $runtime = array();

    private $debugMode = false;

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
        return $this->$description;
    }

    public function onSuccess($alias = "")
    {
        if ($this->debugMode) {
            $className = get_class($this);
            applLog("Parser:onSuccess", "[Name:$this->name] of <$className>");
        }

    }

    public function onError($alias = "")
    {
        if ($this->debugMode) {
            $className = get_class($this);
            applLog("Parser:onError", "[Name:$this->name] of <$className>");
        }

    }

    public function onTry($alias = "")
    {
        if ($this->debugMode) {
            $className = get_class($this);
            applLog("Parser:onTry", "[Name:$this->name] of <$className>");
        }

    }
}

?>