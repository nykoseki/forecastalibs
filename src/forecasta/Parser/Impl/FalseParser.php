<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Common\Historical;
use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserContext;
use Forecasta\Parser\HistoryEntry;

//use Forecasta\Parser\Impl\ParserTrait as PST;

/**
 * 常にパースに失敗するパーサです
 * @author nkoseki
 *
 */
class FalseParser implements Parser
{
    use ParserTrait;
    use Historical;

    /**
     * パースメソッド
     * @param ParserContext $ctx
     * @return ParserContext コンテキスト
     */
    public function parse($context, $depth=0, HistoryEntry $currentEntry = null)
    {
        // 深度計算
        $depth = $depth + 1;

        // 履歴登録
        $context->setParser($this);
        $context->setName($this->getName());
        if($currentEntry == null) {
            $currentEntry = HistoryEntry::createEntry($this->getName(), $context->copy(), $this);
            $currentEntry->setDepth($depth);
        }

        $this->onTry($depth);

        // 履歴enter処理
        $currentEntry->enter($this, $context->copy());

        $currentCtx = ParserContext::getBlank();

        $ctx = new ParserContext($context->target(), $context->current(), null, false);

        $this->onError($ctx, $depth);
        $currentEntry->leave($this, $ctx->copy(), false);

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
        //$this->parserHistoryEntry = new P\HistoryEntry;
    }

    public function __toString()
    {
        $searched = array();
        return $this->outputRecursive($searched);
    }

    public function outputRecursive($searched)
    {
        $className = get_class($this);
        applLog2("outputRecursive", $searched);
        $searched[] = $this->name;

        $className = str_replace("\\", "/", $className);

        $name = $this->name;
        $param = '';
        $message = "{\"Type\":\"$className\", \"Name\":\"$name\", \"Param\":\"$param\"}";

        return $message;
    }
}