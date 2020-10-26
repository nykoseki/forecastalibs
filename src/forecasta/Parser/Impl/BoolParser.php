<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Common\Historical;
use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserContext;
use Forecasta\Parser\HistoryEntry;
use Forecasta\Parser\ParserFactory;

/**
 * Boolean(trueまたはfalse)を表すパーサです
 * @author nkoseki
 *
 */
class BoolParser implements Parser
{
    use ParserTrait;
    use Historical;

    private $parser = null;

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

        //$depth = $depth + 1;
        $this->onTry($depth);
        // 履歴enter処理
        $currentEntry->enter($this, $context->copy());

        //$currentCtx = ParserContext::getBlank();

        /*
        $childHistory = HistoryEntry::createEntry($this->getName(), $context->copy(), $this);
        $childHistory->setParentEntry($this->getHistory());

        $childHistory->enter($this, $context);
        */
        $ctx = $this->parser->parse($context, $depth + 1);


        if($ctx->result()) {
            $this->onSuccess($ctx, $depth);
            $currentEntry->leave($this, $ctx->copy(), true);
        } else {
            $this->onError($ctx, $depth);
            $currentEntry->leave($this, $ctx->copy(), false);
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

        //$this->parserHistoryEntry = new HistoryEntry;
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
        applLog2("outputRecursive", $searched);
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