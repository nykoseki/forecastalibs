<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser as P;
use Forecasta\Parser\Impl\ParserTrait as PST;

/**
 * 指定された文字列にマッチした場合にパース成功とするパーサです
 * @author nkoseki
 *
 */
class TokenParser implements P\Parser
{
    use PST;

    private $str;

    /**
     * パースメソッド
     * @param ParserContext $ctx
     * @return ParserContext コンテキスト
     */
    public function parse($context)
    {
        $this->onTry();
        $len = mb_strlen($this->str);
        
        //applLog("CharParser", $context);

        if (mb_substr($context->target(), $context->current(), $len) === $this->str) {


            $parsed = $this->str;
            $parsed = $this->decolateParsed($parsed);

            $ctx = new P\ParserContext($context->target(), $context->current() + $len, $parsed, true);

            $this->onSuccess($ctx);

            $ctx->setParsedBy($this);

            return $ctx;
        } else {

            $ctx = new P\ParserContext($context->target(), $context->current(), null, false);

            $this->onError($ctx);

            $ctx->setParsedBy($this);

            return $ctx;
        }
    }

    public function __construct($str)
    {
        $this->str = $str;
        //$this->name = 'Anonymous_' . md5(rand());
        $this->name = "Token";
        $this->parserHistoryEntry = new P\HistoryEntry;
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
        applLog("outputRecursive", $searched);
        $searched[] = $this->name;

        $className = str_replace("\\", "/", $className);

        $name = $this->name;
        $param = $this->str;
        $message = "{\"Type\":\"$className\", \"Name\":\"$name\", \"Param\":\"$param\"}";

        return $message;
    }
}

?>