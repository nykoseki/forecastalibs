<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser as P;
use Forecasta\Parser\Impl\ParserTrait as PST;

/**
 * 登録されたパーサのうち，いづれかのパーサがパース成功した場合にパースが成功するパーサコンビネータです
 * @author nkoseki
 *
 */
class ChoiceParser implements P\Parser, P\HasMoreChildren
{
    use PST;

    private $parsers = [];

    /**
     * パースメソッド
     * @param ParserContext $ctx
     * @return ParserContext コンテキスト
     */
    public function parse($context)
    {
        $this->onTry();
        $result = [];

        //applLog("ChoiceParser", $this->parsers);

        $currentParsed = $context;

        for ($i = 0; $i < count($this->parsers); $i++) {
            $currentParser = $this->parsers[$i];
            $currentParsed = $currentParser->parse($currentParsed);

            if ($currentParsed->result() === true) {


                $ctx = $currentParsed;

                $this->onSuccess($ctx);

                return $ctx;
            }
        }

        $ctx = new P\ParserContext($context->target(), $currentParsed->current(), null, false);

        $ctx->setParsedBy($this);

        $this->onError($ctx);

        return $ctx;
    }

    public function __construct()
    {
        //$this->name = 'Anonymous_' . md5(rand());
        $this->name = "Choice";
        $this->parserHistoryEntry = new P\HistoryEntry;
    }

    public function add(P\Parser $parser)
    {
        array_push($this->parsers, $parser);

        return $this;
    }

    public function isResolved()
    {
        return count($this->parsers) > 0;
    }

    public function __toString()
    {
        $searched = array();
        return $this->outputRecursive($searched);
    }

    public function outputRecursive($searched)
    {
        $className = get_class($this);

        $className = str_replace("\\", "/", $className);
        applLog("outputRecursive", $searched);
        $searched[] = $this->name;

        $childMessageAry = [];
        foreach ($this->parsers as $child) {
            if ($child instanceof P\HasMoreChildren) {
                $childMessageAry[] = $child->outputRecursive($searched);
            } else {
                $childMessageAry[] = $child->outputRecursive($searched);
            }

        }

        $name = $this->name;
        $param = '[' . implode(', ', $childMessageAry) . ']';
        $message = "{\"Type\":\"$className\", \"Name\":\"$name\", \"Param\":$param}";

        return $message;
    }
}

?>