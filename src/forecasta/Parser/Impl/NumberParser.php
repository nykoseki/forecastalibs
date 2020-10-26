<?php
/**
 * Created by PhpStorm.
 * User: nykos
 * Date: 2020/10/26
 * Time: 21:39
 */
namespace Forecasta\Parser\Impl;

use Forecasta\Common\Historical;
use Forecasta\Parser\HistoryEntry;
use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserContext;
use Forecasta\Parser\HasMoreChildren;
//use Forecasta\Parser as P;
use Forecasta\Parser\Impl\ParserTrait as PST;
use Forecasta\Parser\ParserFactory;

class NumberParser implements Parser
{
    use ParserTrait;
    use Historical;

    private $parser = null;

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

        // 履歴エントリ作成
        $childHistory = HistoryEntry::createEntry($this->parser->getName(), $context->copy(), $this->parser);
        $currentEntry->addEntry($childHistory);

        $ctx = $this->parser->parse($context, $depth, $childHistory);

        if($ctx->result()) {
            $this->onSuccess($ctx, $depth);

            // 履歴leave処理
            $currentEntry->leave($this, $ctx->copy(), true);
        } else {
            $this->onError($ctx, $depth);

            // 履歴leave処理
            $currentEntry->leave($this, $ctx->copy(), false);
        }

        return $ctx;
    }

    public function __construct()
    {
        // Number
        $number = ParserFactory::Regex("/^[0-9]+/")->setName("Number");

        $this->parser = $number;

        $this->name = "Number";
    }

    public function isResolved()
    {
        return true;
    }

    public function __toString()
    {
        $searched = array();
        return $this->outputRecursive($searched);
    }

    public function outputRecursive($searched)
    {
        return $this->parser->outputRecursive($searched). "";
    }
}