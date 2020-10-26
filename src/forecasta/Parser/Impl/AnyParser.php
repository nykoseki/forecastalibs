<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Common\Historical;
use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserContext;
use Forecasta\Parser\HasMoreChildren;
use Forecasta\Parser\HistoryEntry;

/**
 * 登録されたパーサの1回以上の繰り返しを表すパーサコンビネータです
 * @author nkoseki
 *
 */
class AnyParser implements Parser, HasMoreChildren
{
    use ParserTrait;
    use Historical;

    private $parser;

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
        $result = [];
        // 履歴enter処理
        $currentEntry->enter($this, $context->copy());

        //$currentCtx = ParserContext::getBlank();

        //$position = $context->current();

        $currentParsed = $context;

        $isSuccess = false;
        for (; ;) {
            // 履歴エントリ作成
            $childHistory = HistoryEntry::createEntry($this->parser->getName(), $currentParsed->copy(), $this->parser);
            //$currentEntry->addEntry($childHistory);

            //$childHistory->enter($this, $currentParsed->copy());
            $currentParsed = $this->parser->parse($currentParsed->copy(), $depth, $childHistory);

            if ($currentParsed->result() === true) {
                array_push($result, $currentParsed->parsed());

                $isSuccess = $isSuccess || true;

                $currentEntry->addEntry($childHistory);

                //$position = $currentParsed->current();
            } else {
                break;
            }
        }

        if($isSuccess) {

            $ctx = new ParserContext($context->target(), $currentParsed->current(), $result, true);

            $this->onSuccess($ctx, $depth);
            // 履歴leave処理
            $currentEntry->leave($this, $ctx->copy(), true);

            return $ctx;
        } else {
            $ctx = new ParserContext($context->target(), $context->current(), null, false);

            $this->onError($ctx, $depth);
            // 履歴leave処理
            $currentEntry->leave($this, $ctx->copy(), false);

            return $ctx;
        }

        /*
        $this->onSuccess();
        return new P\ParserContext($context->target(), $currentParsed->current(), $result, true);
        */
    }

    public function add(Parser $parser)
    {
        $this->parser = $parser;

        return $this;
    }

    public function __construct(/*P\Parser $parser*/)
    {
        /*$this->parser = $parser;*/
        //$this->name = 'Anonymous_' . md5(rand());
        $this->name = "Any";

        //$this->parserHistoryEntry = new HistoryEntry;
    }

    public function isResolved()
    {
        return isset($this->parser);
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
        if ($this->parser instanceof HasMoreChildren) {
            $param = $this->parser->outputRecursive($searched);
        } else {
            $param = $this->parser->outputRecursive($searched);
        }

        $message = "{\"Type\":\"$className\", \"Name\":\"$name\", \"Param\":$param}";

        return $message;
    }
}