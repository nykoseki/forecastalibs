<?php
/**
 * Created by PhpStorm.
 * User: nykos
 * Date: 2020/10/27
 * Time: 15:15
 */


namespace Forecasta\Parser\Impl;

use Forecasta\Common\Historical;
use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserContext;
use Forecasta\Parser\HistoryEntry;


/**
 * 指定された文字列が"}"にマッチした場合マッチ成功とするパーサです
 * @author nkoseki
 *
 */
class ObjectRightParser implements Parser
{
    use ParserTrait;
    use Historical;

    private $chars = "}";

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

        $this->onTry($depth);

        // 履歴enter処理
        $currentEntry->enter($this, $context->copy());

        $targetArray = [];

        //$currentCtx = ParserContext::getBlank();

        $strArray = str_split($this->chars);

        array_map(function ($s) use (&$targetArray) {
            $targetArray[$s] = [$s];
        }, $strArray);

        $target0 = mb_substr($context->currentTarget(), 0, 1);

        //applLog2("CharParser", $target0);
        if (array_key_exists($target0, $targetArray)) {

            //$target0 = $this->decolateParsed($target0);

            $ctx = new ParserContext($context->target(), $context->current() + 1, $target0, true);

            //$ctx->updateParsed("<Joint>");
            $this->onSuccess($ctx, $depth);
            $ctx->updateParsed("}");

            // 履歴leave処理
            $currentEntry->leave($this, $ctx->copy(), true);

            return $ctx;
        } else {
            $ctx = (new FalseParser())->parse($context);

            $this->onError($ctx, $depth);

            // 履歴leave処理
            $currentEntry->leave($this, $ctx->copy(), false);

            return $ctx;
        }
    }

    public function __construct($objectLeft = "}")
    {
        //$this->chars = $chars;
        //$this->parserHistoryEntry = new P\HistoryEntry;

        //$this->name = 'Anonymous_' . md5(rand());

        $this->chars = $objectLeft;
        $this->name = "Joint";
    }

    public function isResolved()
    {
        return isset($this->chars);
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

        $name = $this->name;
        $param = $this->chars;
        $message = "{\"Type\":\"$className\", \"Name\":\"$name\", \"Param\":\"$param\"}";

        return $message;
    }
}