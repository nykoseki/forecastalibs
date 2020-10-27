<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Common\Historical;
use Forecasta\Parser\HistoryEntry;
use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserContext;
//use Forecasta\Parser as P;
//use Forecasta\Parser\Impl\ParserTrait as PST;
use Forecasta\Parser\ParserFactory;

/**
 * 改行およびホワイトスペース・タブ文字の連続を表すパーサです
 * @author nkoseki
 *
 */
class LbWsParser implements Parser
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

        $currentCtx = ParserContext::getBlank();

        // 履歴エントリ作成
        $childHistory = HistoryEntry::createEntry($this->parser->getName(), $context->copy(), $this->parser);
        //$currentEntry->addEntry($childHistory);

        $ctx = $this->parser->parse($context, $depth, $childHistory);

        if($ctx->result()) {
            $ctx->updateParsed("<LbWs>");
            $this->onSuccess($ctx, $depth);

            // 履歴leave処理
            $currentEntry->leave($this, $ctx->copy(), true);

            $currentEntry->addEntry($childHistory);
        } else {
            $this->onError($ctx, $depth);

            // 履歴leave処理
            $currentEntry->leave($this, $ctx->copy(), false);
        }

        return $ctx;
    }

    public function isResolved()
    {
        return true;
    }

    public function __construct()
    {
        $whiteSpace = ParserFactory::Regex("/^\s+/")->skip(true);
        $lineBreak1 = ParserFactory::Token("\n")->skip(true);
        $lineBreak2 = ParserFactory::Token("\r")->skip(true);
        $lineBreak3 = ParserFactory::Token("\r\n")->skip(true);
        $tab = ParserFactory::Token("\t")->skip(true);

        $lbws = ParserFactory::Option()->add(
            ParserFactory::Many()->skip(true)
                ->add(
                    ParserFactory::Choice()->skip(true)
                        ->add($tab)
                        ->add($whiteSpace)
                        ->add(
                            ParserFactory::Choice()
                                ->add($lineBreak1)
                                ->add($lineBreak2)
                                ->add($lineBreak3)
                        )
                )
        );


        //$whiteSpace = ParserFactory::Seq()->add($whiteSpace)->add($lineBreak)->add($whiteSpace);
        $this->parser = $lbws;
        $this->parser->skip(true);

        //$this->parserHistoryEntry = new P\HistoryEntry;

        $this->name = "LbWs";
    }

    public function __toString()
    {
        $searched = array();
        return $this->outputRecursive($searched);
    }

    public function outputRecursive($searched)
    {
        /*
        $className = get_class($this);
        applLog2("outputRecursive", $searched);
        $searched[] = $this->name;

        $className = str_replace("\\", "/", $className);

        $name = $this->name;
        $param = '';
        $message = "{\"Type\":\"$className\", \"Name\":\"$name\", \"Param\":\"$param\"}";
        */

        return $this->parser->outputRecursive($searched). "";
    }
}