<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Common\Historical;
use Forecasta\Parser\HistoryEntry;
use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserContext;
use Forecasta\Parser\ParserFactory;

/**
 * ダブルクォートによる空文字を判定するパーサです
 * @author nkoseki
 *
 */
class EmptyParser implements Parser
{
    use ParserTrait;
    use Historical;

    private $parser = null;


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

        $resultCtx = $this->parser->parse($context, $depth);

        $currentCtx = ParserContext::getBlank();

        if($resultCtx->result()) {
            /*
             * $target, $position, $parsed, $ctx
             */

            $ctx = new ParserContext($resultCtx->target(), $resultCtx->current(), $resultCtx->parsed(), $resultCtx->result());
            //$ctx = new P\ParserContext($resultCtx->target(), $resultCtx->current(), "<Empty>", $resultCtx->result());

            if($ctx->result()) {
                //$ctx->setParsed("<Empty>");
                $ctx->updateParsed("<Empty>");
                $this->onSuccess($ctx, $depth);

                $currentEntry->leave($this, $ctx->copy(), true);
            } else {
                $this->onError($ctx, $depth);

                $currentEntry->leave($this, $ctx->copy(), false);
            }

            return $ctx;
        }

        $ctx = $this->parser->parse($context, $depth);

        $currentEntry->leave($this, $ctx->copy(), $ctx->result());

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

        //$this->parserHistoryEntry = new P\HistoryEntry;
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