<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser as P;
use Forecasta\Parser\Impl\ParserTrait as PST;

/**
 * 常にパースに失敗するパーサです
 * @author nkoseki
 *
 */
class FalseParser implements P\Parser
{
    use PST;

    /**
     * パースメソッド
     * @param ParserContext $ctx
     * @return ParserContext コンテキスト
     */
    public function parse($context, $depth=0)
    {
        $depth = $depth + 1;
        $this->onTry($depth);

        $currentCtx = P\ParserContext::getBlank();

        $ctx = new P\ParserContext($context->target(), $context->current(), null, false);

        $this->onError($ctx, $depth);

        return $ctx;
    }

    public function isResolved()
    {
        return true;
    }

    public function __construct()
    {
        $this->name = 'Anonymous_' . md5(rand());
        $this->name = "False";
        $this->parserHistoryEntry = new P\HistoryEntry;
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
        $param = '';
        $message = "{\"Type\":\"$className\", \"Name\":\"$name\", \"Param\":\"$param\"}";

        return $message;
    }
}