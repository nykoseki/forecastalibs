<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Common\Historical;
use Forecasta\Parser\HistoryEntry;
use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserContext;
use Forecasta\Parser\HasMoreChildren;
//use Forecasta\Parser as P;
//use Forecasta\Parser as P;
//use Forecasta\Parser\Impl\ParserTrait as PST;

/**
 * 登録されたパーサの0回以上の繰り返しを表すパーサコンビネータです
 * @author nkoseki
 *
 */
class ManyParser implements Parser, HasMoreChildren
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

        // 履歴enter処理
        $currentEntry->enter($this, $context->copy());

        $result = [];

        $currentCtx = ParserContext::getBlank();

        $currentParsed = $context;

        $ctxArray = array();

        for (; ;) {
            // 履歴エントリ作成
            $childHistory = HistoryEntry::createEntry($this->parser->getName(), $context->copy(), $this->parser);
            //$currentEntry->addEntry($childHistory);

            $currentParsed = $this->parser->parse($currentParsed, $depth, $childHistory);

            if ($currentParsed->result() === true) {
                //if($this->parser->isSkip() === false) {
                //    array_push($result, $currentParsed->parsed());
                //}
                array_push($result, $currentParsed->parsed());
                $currentParsed->setParent($context);
                array_push($ctxArray, $currentParsed);

                $currentEntry->addEntry($childHistory);
            } else {
                break;
            }
        }

        $ctx = new ParserContext($context->target(), $currentParsed->current(), $result, true);

        $this->onSuccess($ctx, $depth);

        // 履歴leave処理
        $currentEntry->leave($this, $ctx->copy(), true);

        foreach($ctxArray as $v) {
            $ctx->add($v);
        }

        return $ctx;
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
        $this->name = "Many";
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