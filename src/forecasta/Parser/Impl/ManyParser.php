<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser as P;
use Forecasta\Parser\Impl\ParserTrait as PST;

/**
 * 登録されたパーサの0回以上の繰り返しを表すパーサコンビネータです
 * @author nkoseki
 *
 */
class ManyParser implements P\Parser, P\HasMoreChildren
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

        $currentParsed = $context;

        $ctxArray = array();

        for (; ;) {
            $currentParsed = $this->parser->parse($currentParsed, $depth);
            if ($currentParsed->result() === true) {
                //if($this->parser->isSkip() === false) {
                //    array_push($result, $currentParsed->parsed());
                //}
                array_push($result, $currentParsed->parsed());
                $currentParsed->setParent($context);
                array_push($ctxArray, $currentParsed);
            } else {
                break;
            }
        }

        $ctx = new P\ParserContext($context->target(), $currentParsed->current(), $result, true);

        $this->onSuccess($ctx, $depth);

        foreach($ctxArray as $v) {
            $ctx->add($v);
        }

        return $ctx;
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
        $this->name = "Many";
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
        Forecasta\Common\applLog("outputRecursive", $searched);
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