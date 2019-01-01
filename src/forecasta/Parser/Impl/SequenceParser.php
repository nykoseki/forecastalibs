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
    public function parse($context)
    {
        $this->onTry();
        $result = [];

        $currentParsed = $context;

        $ctxArray = array();

        for ($i = 0; $i < count($this->parsers); $i++) {
            $currentParser = $this->parsers[$i];
            $currentParsed = $currentParser->parse($currentParsed);

            //$context->add($currentParsed);

            if ($currentParsed->result() === true) {
                array_push($result, $currentParsed->parsed());

                $currentParsed->setParent($context);
                array_push($ctxArray, $currentParsed);

            } else {
                $ctx = (new P\Impl\FalseParser())->parse($context);

                $ctx->setParsedBy($this);

                $this->onError($ctx);

                return $ctx;
            }
        }

        $ctx = new P\ParserContext($context->target(), $currentParsed->current(), $result, true);

        foreach($ctxArray as $v) {
            $ctx->add($v);
        }

        $this->onSuccess($ctx);

        $ctx->setParsedBy($this);

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
// 			if($child instanceof P\HasMoreChildren) {
// 				$childMessageAry[] = $child->outputRecursive($searched);
// 			} else {
// 				$childMessageAry[] = $child->outputRecursive($searched);
// 			}

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