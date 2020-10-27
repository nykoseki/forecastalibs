<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Common\Historical;
use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserContext;
use Forecasta\Parser\HasMoreChildren;
use Forecasta\Parser\HistoryEntry;
//use Forecasta\Parser\Impl\ParserTrait as PST;

/**
 * 登録されたパーサのうち，いづれかのパーサがパース成功した場合にパースが成功するパーサコンビネータです
 * @author nkoseki
 *
 */
class ChoiceParser implements Parser, HasMoreChildren
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
        $depth = $depth + 1;

        // 履歴登録
        $context->setParser($this);
        $context->setName($this->getName());
        if($currentEntry == null) {
            $currentEntry = HistoryEntry::createEntry($this->getName(), $context->copy(), $this);
            $currentEntry->setDepth($depth);
        }

        $depth = $depth + 1;
        $this->onTry($depth);
        $result = [];

        // 履歴enter処理
        $currentEntry->enter($this, $context->copy());

        //$currentCtx = ParserContext::getBlank();

        //applLog2("ChoiceParser", $this->parsers);

        $currentParsed = $context;

        for ($i = 0; $i < count($this->parsers); $i++) {
            $currentParser = $this->parsers[$i];

            //echo $this->getName(). " => ". $currentParser->getName(). "\n";

            // 履歴エントリ作成
            $childHistory = HistoryEntry::createEntry($currentParser->getName(), $currentParsed->copy(), $currentParser);
            //$childHistory->setParentEntry($currentEntry);
            //$currentEntry->addEntry($childHistory);

            $currentParsed = $currentParser->parse($currentParsed, $depth, $childHistory);


            if ($currentParsed->result() === true) {

                $ctx = $currentParsed;

                $this->onSuccess($ctx, $depth);

                $currentEntry->leave($this, $currentParsed->copy(), true);

                $currentEntry->addEntry($childHistory);

                return $ctx;
            }
        }

        $ctx = new ParserContext($context->target(), $currentParsed->current(), null, false);

        $this->onError($ctx, $depth);

        $currentEntry->leave($this, $currentParsed->copy(), false);

        return $ctx;
    }

    public function __construct()
    {
        //$this->name = 'Anonymous_' . md5(rand());
        $this->name = "Choice";
        //$this->parserHistoryEntry = new HistoryEntry;
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

        $className = str_replace("\\", "/", $className);
        applLog2("outputRecursive", $searched);
        $searched[] = $this->name;

        $childMessageAry = [];
        foreach ($this->parsers as $child) {
            if ($child instanceof HasMoreChildren) {
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