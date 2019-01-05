<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser as P;
use Forecasta\Parser\Impl\ParserTrait as PST;

/**
 * 指定されたパーサがパースに失敗しても，パース成功とするパーサコンビネータです
 * @author nkoseki
 *
 */
class OptionParser implements P\Parser, P\HasMoreChildren
{
    use PST;

    private $parser;

    /**
     * パースメソッド
     * @param ParserContext $ctx
     * @return ParserContext コンテキスト
     */
    public function parse($context, $depth=0)
    {
        $depth = $depth + 1;
        $this->onTry($depth);
        $result = [];

        $currentCtx = P\ParserContext::getBlank();

        $currentParsed = $this->parser->parse($context, $depth);

        if ($currentParsed->result()) {
            $context->add($currentParsed);
            $currentParsed->setParent($context);

            $this->onSuccess($currentParsed);

            return $currentParsed;
        } else {
            
            $ctx = (new P\Impl\TrueParser())->parse($context, $depth);

            $this->onSuccess($ctx, $depth);

            $context->add($ctx);
            $ctx->setParent($context);

            return $ctx;
        }
    }

    public function add(P\Parser $parser)
    {
        $this->parser = $parser;

        return $this;
    }

    public function __construct(/*P\Parser $parser*/)
    {
        /*$this->parser = $parser;*/
        //$this->name = 'Anonymous_' . md5(rand());
        $this->name = "Option";
        $this->parserHistoryEntry = new P\HistoryEntry;
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
        applLog("outputRecursive", $searched);
        $searched[] = $this->name;

        $className = str_replace("\\", "/", $className);

        $name = $this->name;
        if ($this->parser instanceof P\HasMoreChildren) {
            $param = $this->parser->outputRecursive($searched);
        } else {
            $param = $this->parser->outputRecursive($searched);
        }

        $message = "{\"Type\":\"$className\", \"Name\":\"$name\", \"Param\":$param}";

        return $message;
    }
}

?>