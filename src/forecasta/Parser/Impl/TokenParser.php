<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Common\Historical;
use Forecasta\Parser\HistoryEntry;
use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserContext;
//use Forecasta\Parser\Impl\ParserTrait as PST;

/**
 * 指定された文字列にマッチした場合にパース成功とするパーサです
 * @author nkoseki
 *
 */
class TokenParser implements Parser
{
    use ParserTrait;
    use Historical;

    private $str;

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

        $len = mb_strlen($this->str);

        if (mb_substr($context->target(), $context->current(), $len) === $this->str) {
            $parsed = $this->str;

            $ctx = new ParserContext($context->target(), $context->current() + $len, $parsed, true);

            $this->onSuccess($ctx, $depth);

            // 履歴leave処理
            $currentEntry->leave($this, $ctx, true);

            return $ctx;
        } else {
            $ctx = (new FalseParser())->parse($context);
            $this->onError($ctx, $depth);

            // 履歴leave処理
            $currentEntry->leave($this, $ctx, false);

            return $ctx;
        }
    }

    public function __construct($str)
    {
        $this->str = $str;
        //$this->name = 'Anonymous_' . md5(rand());
        $this->name = "Token";
        //$this->parserHistoryEntry = new P\HistoryEntry;
    }

    public function isResolved()
    {
        return isset($this->str);
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
        $param = $this->str;
        $message = "{\"Type\":\"$className\", \"Name\":\"$name\", \"Param\":\"$param\"}";

        return $message;
    }
}
