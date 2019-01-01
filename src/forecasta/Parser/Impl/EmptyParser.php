<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser as P;
use Forecasta\Parser\Impl\ParserTrait as PST;
use Forecasta\Parser\ParserFactory;

/**
 * ダブルクォートによる空文字を判定するパーサです
 * @author nkoseki
 *
 */
class EmptyParser implements P\Parser
{
    use PST;

    private $parser = null;

    /**
     * パースメソッド
     * @param ParserContext $ctx
     * @return ParserContext コンテキスト
     */
    public function parse($context)
    {
        $resultCtx = $this->parser->parse($context);

        if($resultCtx->result()) {
            /*
             * $target, $position, $parsed, $ctx
             */

            $this->onTry();

            $ctx = new P\ParserContext($resultCtx->target(), $resultCtx->current(), "<Empty>", $resultCtx->result());

            $ctx->setParsedBy($this);

            if($ctx->result()) {
                $this->onSuccess($ctx);
            } else {
                $this->onError($ctx);
            }

            return $ctx;
        }

        $ctx = $this->parser->parse($context);
        return $ctx;
    }

    public function isResolved()
    {
        return true;
    }

    public function __construct()
    {
        //$this->name = 'Anonymous_' . md5(rand());
        $quote = ParserFactory::Token("\"");
        $squote = ParserFactory::Token("'");

        $psr = ParserFactory::Choice()->add(
            ParserFactory::Seq()->add($quote)->add($quote)->setName("Empty")
        )->add(
            ParserFactory::Seq()->add($squote)->add($squote)->setName("Empty")
        );

        //$this->parser = ParserFactory::Seq()->add($quote)->add($quote)->setName("Empty");
        $this->parser = $psr;

        $this->name = "Empty";

        $this->parserHistoryEntry = new P\HistoryEntry;
    }

    public function __toString()
    {
        $searched = array();
        return $this->outputRecursive($searched);
    }

    public function outputRecursive($searched)
    {
        return "<Empty>";
    }
}