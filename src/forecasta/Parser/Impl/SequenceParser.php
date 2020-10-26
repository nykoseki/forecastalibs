<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Common\Historical;
use Forecasta\Parser\HistoryEntry;
use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserContext;
use Forecasta\Parser\HasMoreChildren;
//use Forecasta\Parser\Impl\ParserTrait as PST;

/**
 * 登録されたパーサを逐次実行し，すべてのパースに成功した場合パース成功扱いとなるパーサコンビネータです
 * @author nkoseki
 *
 */
class SequenceParser implements Parser, HasMoreChildren
{
    use ParserTrait;
    use Historical;

    private $parsers = [];

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

        $currentParsed = $context;

        for ($i = 0; $i < count($this->parsers); $i++) {
            $currentParser = $this->parsers[$i];

            // 履歴エントリ作成
            $childHistory = HistoryEntry::createEntry($currentParser->getName(), $currentParsed->copy(), $currentParser);
            //$currentEntry->addEntry($childHistory);

            $currentParsed = $currentParser->parse($currentParsed, $depth, $childHistory);

            if ($currentParsed->result() === true) {
                if($currentParser->isSkip() === false) {
                    array_push($result, $currentParsed->parsed());
                    $currentEntry->addEntry($childHistory);
                }
                //array_push($result, $currentParsed->parsed());

                $currentParsed->setParent($context);
            } else {
                $ctx = (new FalseParser())->parse($context, $depth);

                $this->onError($ctx, $depth);

                // 履歴leave処理
                $currentEntry->leave($this, $ctx->copy(), false);

                return $ctx;
            }
        }

        $ctx = new ParserContext($context->target(), $currentParsed->current(), $result, true);
        $this->onSuccess($ctx, $depth);

        // 履歴leave処理
        $currentEntry->leave($this, $ctx->copy(), true);

        return $ctx;
    }

    public function __construct()
    {
        //$this->name = 'Anonymous_' . md5(rand());
        $this->name = "Sequence";
        //$this->parserHistoryEntry = new P\HistoryEntry;
    }

    public function add(Parser $parser)
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
        applLog2("outputRecursive", $searched);
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