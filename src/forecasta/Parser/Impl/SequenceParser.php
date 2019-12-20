<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser as P;
use Forecasta\Parser\Impl\ParserTrait as PST;

/**
 * 登録されたパーサを逐次実行し，すべてのパースに成功した場合パース成功扱いとなるパーサコンビネータです
 * @author nkoseki
 *
 */
class SequenceParser implements P\Parser, P\HasMoreChildren
{
    use PST;

    private $parsers = [];

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

        $currentParsed = $context;

        for ($i = 0; $i < count($this->parsers); $i++) {
            $currentParser = $this->parsers[$i];
            $currentParsed = $currentParser->parse($currentParsed, $depth);

            if ($currentParsed->result() === true) {
                if($currentParser->isSkip() === false) {
                    array_push($result, $currentParsed->parsed());
                }
                //array_push($result, $currentParsed->parsed());

                $currentParsed->setParent($context);
            } else {
                $ctx = (new P\Impl\FalseParser())->parse($context, $depth);

                $this->onError($ctx, $depth);
                return $ctx;
            }
        }

        $ctx = new P\ParserContext($context->target(), $currentParsed->current(), $result, true);
        $this->onSuccess($ctx, $depth);

        return $ctx;
    }

    public function __construct()
    {
        //$this->name = 'Anonymous_' . md5(rand());
        $this->name = "Sequence";
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
        applLog("outputRecursive", $searched);
        $searched[] = $this->name;

        $className = str_replace("\\", "/", $className);

        $childMessageAry = [];
        foreach ($this->parsers as $child) {

            if (array_search($child->getName(), $searched) > -1) {
                $childName = $child->getName();
                $childCls = get_class($child);
                $childCls = str_replace("\\", "/", $childCls);

                //$tmpParam = "\"<$childName|$childCls>\"";

                $childMessageAry[] = "{\"Type\":\"$childCls\", \"Name\":\"$childName\"}";
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