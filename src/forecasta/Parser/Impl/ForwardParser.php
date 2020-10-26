<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Common\Historical;
use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserContext;
use Forecasta\Parser\HasMoreChildren;
use Forecasta\Parser\HistoryEntry;
//use Forecasta\Parser as P;
//use Forecasta\Parser\Impl\ParserTrait as PST;

/**
 * 登録されたパーサを遅延処理で提供するパーサコンビネータです
 * @author nkoseki
 *
 */
class ForwardParser implements Parser, HasMoreChildren
{
    use ParserTrait;
    use Historical;

    private $forwarder;

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

        $currentCtx = ParserContext::getBlank();

        //$parser = $this->forwarder->__invoke();

        // 履歴エントリ作成
        $childHistory = HistoryEntry::createEntry($this->forwarder->getName(), $context->copy(), $this->forwarder);
        //$currentEntry->addEntry($childHistory);

        $ctx = $this->forwarder->parse($context, $depth, $childHistory);

        if($ctx->result()) {
            $this->onSuccess($ctx, $depth);

            // 履歴leave処理
            $currentEntry->leave($this, $ctx->copy(), true);

            $currentEntry->addEntry($childHistory);
        } else {
            $this->onError($ctx, $depth);

            // 履歴leave処理
            $currentEntry->leave($this, $ctx->copy(), false);
        }

        //return $parser->parse($context);
        return $ctx;
    }

    /**
     * このパーサのparseメソッドがコールされた際に移譲されるパーサを設定します
     * @param P\Parser $parser
     * @return $this
     */
    public function forward(Parser $parser)
    {
        /*
        $this->forwarder = function () use (&$parser) {
            return $parser;
        };
        */

        $this->forwarder = $parser;

        return $this;
    }

    public function __construct()
    {
        $this->name = 'Anonymous_' . md5(rand());
        $this->name = "Forward";
        $this->parserHistoryEntry = new HistoryEntry;
    }

    public function isResolved()
    {
        return isset($this->forwarder);
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

        $name = $this->name;
        $param = '';
        if (!is_null($this->forwarder) && $this->forwarder/*->__invoke()*/ instanceof Parser) {
            $forwardedFor = $this->forwarder/*->__invoke()*/;

            $cls = get_class($forwardedFor);

            if (array_search($forwardedFor->getName(), $searched) > -1) {
                $cls = str_replace("\\", "/", $cls);

                $forwardedForName = $forwardedFor->getName();
                $param = "\"<$forwardedForName|$cls>\"";
            } else {
                $cls = str_replace("\\", "/", $cls);

                $forwardedForName = $forwardedFor->getName();
                //$param = $forwardedFor->outputRecursive($searched);
                return $forwardedFor->outputRecursive($searched);
            }

        } else {
            $param = "\"<Unkwnon>\"";
        }
        $message = "{\"Type\":\"$className\", \"Name\":\"$name\", \"Param\":$param}";

        return $message;
    }


}

?>