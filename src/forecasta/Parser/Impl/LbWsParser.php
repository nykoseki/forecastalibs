<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser as P;
use Forecasta\Parser\Impl\ParserTrait as PST;
use Forecasta\Parser\ParserFactory;

/**
 * 改行およびホワイトスペースを表すパーサです
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
    public function parse($context)
    {
        $this->onTry();

        $ctx = $this->parser->parse($context);


        $ctx->setParsedBy($this);

        if($ctx->result()) {
            $this->onSuccess($ctx);
        } else {
            $this->onError($ctx);
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
        $whiteSpace = ParserFactory::Option()->add(ParserFactory::Regex("/^\s+/"));
        $lineBreak = ParserFactory::Option()->add(ParserFactory::Token("\n"));

        $whiteSpace = ParserFactory::Seq()->add($whiteSpace)->add($lineBreak)->add($whiteSpace);
        $this->parser = $whiteSpace;

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
        applLog("outputRecursive", $searched);
        $searched[] = $this->name;

        $className = str_replace("\\", "/", $className);

        $name = $this->name;
        $param = '';
        $message = "{\"Type\":\"$className\", \"Name\":\"$name\", \"Param\":\"$param\"}";
        */

        return $this->parser->outputRecursive($searched). "";
    }
}