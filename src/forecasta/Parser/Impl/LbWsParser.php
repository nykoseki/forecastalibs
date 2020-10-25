<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser as P;
use Forecasta\Parser\Impl\ParserTrait as PST;
use Forecasta\Parser\ParserFactory;

/**
 * 改行およびホワイトスペース・タブ文字の連続を表すパーサです
 * @author nkoseki
 *
 */
class LbWsParser implements P\Parser
{
    use PST;

    private $parser = null;

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

        $ctx = $this->parser->parse($context, $depth);

        if($ctx->result()) {
            $ctx->updateParsed("<LbWs>");
            $this->onSuccess($ctx, $depth);
        } else {
            $this->onError($ctx, $depth);
        }

        return $ctx;
    }

    public function isResolved()
    {
        return true;
    }

    public function __construct()
    {
        $whiteSpace = ParserFactory::Regex("/^\s+/");
        $lineBreak1 = ParserFactory::Token("\n");
        $lineBreak2 = ParserFactory::Token("\r");
        $lineBreak3 = ParserFactory::Token("\r\n");
        $tab = ParserFactory::Token("\t");

        $lbws = ParserFactory::Option()->add(
            ParserFactory::Many()
                ->add(
                    ParserFactory::Choice()
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

        $this->parserHistoryEntry = new P\HistoryEntry;

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