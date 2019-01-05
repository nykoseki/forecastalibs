<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser as P;
use Forecasta\Parser\Impl\ParserTrait as PST;
use Forecasta\Parser\ParserFactory;

/**
 * Boolean(trueまたはfalse)を表すパーサです
 * @author nkoseki
 *
 */
class BoolParser implements P\Parser
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

        $ctx = $this->parser->parse($context, $depth + 1);

        if($ctx->result()) {
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
        //$this->name = 'Anonymous_' . md5(rand());

        $bool = ParserFactory::Choice()
            ->add(ParserFactory::Token("true"))
            ->add(ParserFactory::Token("false"))
            ->add(ParserFactory::Token("TRUE"))
            ->add(ParserFactory::Token("FALSE"));

        //$bool = ParserFactory::Option()->add(ParserFactory::Regex("/^true|^false|^TRUE|^FALSE/"));

        $this->parser = $bool;
        $this->name = "Bool";

        $this->parserHistoryEntry = new P\HistoryEntry;
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
        applLog("outputRecursive", $searched);
        $searched[] = $this->name;

        $className = str_replace("\\", "/", $className);

        $name = $this->name;
        $param = '';
        $message = "{\"Type\":\"$className\", \"Name\":\"$name\", \"Param\":\"$param\"}";
        */
        if($searched === "true") {
            $searched = "<TRUE($searched)>";
        } else if($searched === "false") {
            $searched = "<FALSE($searched)>";
        } else if($searched === "TRUE") {
            $searched = "<TRUE($searched)>";
        } else if($searched === "FALSE") {
            $searched = "<FALSE($searched)>";
        }

        return $this->parser->outputRecursive($searched). "";
    }
}