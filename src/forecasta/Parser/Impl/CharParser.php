<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser as P;
use Forecasta\Parser\Impl\ParserTrait as PST;

/**
 * 指定された文字列のいづれかの文字にマッチした場合，パース成功とするパーサです
 * @author nkoseki
 *
 */
class CharParser implements P\Parser
{
    use PST;

    private $chars;

    /**
     * パースメソッド
     * @param ParserContext $ctx
     * @return ParserContext コンテキスト
     */
    public function parse($context)
    {
        $this->onTry();
        $targetArray = [];

        $strArray = str_split($this->chars);

        array_map(function ($s) use (&$targetArray) {
            $targetArray[$s] = [$s];
        }, $strArray);

        $target0 = mb_substr($context->currentTarget(), 0, 1);

        //applLog("CharParser", $target0);
        if (array_key_exists($target0, $targetArray)) {
            $this->onSuccess();

            $target0 = $this->decolateParsed($target0);

            return new P\ParserContext($context->target(), $context->current() + 1, $target0, true);
        } else {
            $this->onError();
            return (new P\Impl\FalseParser())->parse($context);
        }
    }

    public function __construct($chars)
    {
        $this->chars = $chars;

        //$this->name = 'Anonymous_' . md5(rand());
    }

    public function isResolved()
    {
        return isset($this->chars);
    }

    public function __toString()
    {
        $searched = array();
        return $this->outputRecursive($searched);
    }

    public function outputRecursive($searched)
    {
        $className = get_class($this);
        applLog("outputRecursive", $searched);
        $searched[] = $this->name;

        $className = str_replace("\\", "/", $className);

        $name = $this->name;
        $param = $this->chars;
        $message = "{\"Type\":\"$className\", \"Name\":\"$name\", \"Param\":\"$param\"}";

        return $message;
    }
}