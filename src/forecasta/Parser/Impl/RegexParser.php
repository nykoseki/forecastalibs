<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Common\Historical;
use Forecasta\Parser\HistoryEntry;
use Forecasta\Parser\Parser;
use Forecasta\Parser\ParserContext;
//use Forecasta\Parser\Impl\ParserTrait as PST;

/**
 * 指定した正規表現文字列にマッチする場合に成功するパーサです
 * @author nkoseki
 *
 */
class RegexParser implements Parser
{
    use ParserTrait;
    use Historical;

    private $regexStr;

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

        $target0 = $context->target();
        $position = $context->current();
        $len = mb_strlen($target0);

        $currentCtx = ParserContext::getBlank();

        $tmpTarget = mb_substr($target0, $position, $len - $position);

        //$tmpRegexp = $this->normalize($this->regexStr);
        $tmpRegexp = $this->regexStr;

        //applLog2("RegexParser:target", $tmpTarget);
        preg_match($tmpRegexp, $tmpTarget, $matches);

        //applLog2("RegexParser:match", $matches);

        if (count($matches) > 0) {
            $match = $matches[0];

            $matchLen = mb_strlen($match);
            $position = $position + $matchLen;

            //applLog2("RegexParser", $matches);


            //$this->setName("Regex-0");

            //$match = $this->decolateParsed($match);

            $ctx = new ParserContext($context->target(), $position, $match, true);


            $this->onSuccess($ctx, $depth);

            // 履歴leave処理
            $currentEntry->leave($this, $ctx->copy(), true);

            //return new P\ParserContext($context->target(), $position, $match, true);
            return $ctx;
        } else {


            $ctx = (new FalseParser())->parse($context, $depth);

            $this->onError($ctx, $depth);

            // 履歴leave処理
            $currentEntry->leave($this, $ctx->copy(), false);

            return $ctx;
        }
    }

    private function normalize($regex)
    {

        preg_match("/\/(.+?)\//", $regex, $matches);

        //applLog2("RegexParser", $matches);

        if (count($matches) > 1) {
            $reg = $matches[1];

            $prefix = mb_substr($reg, 0, 1);
            if ($prefix != '^') {

                //applLog2("RegexParser", '^'. $reg);
                return '/^(' . $reg . ')/m';
                //return '/'. $reg. '/';
            } else {
                return '/' . $reg . '/m';
            }

        } else {
            return "//";
        }
    }

    public function __construct($regex)
    {
        $this->regexStr = $regex;
        //$this->name = 'Anonymous_' . md5(rand());
        //$this->parserHistoryEntry = new P\HistoryEntry;
        $this->name = "Regex";
    }

    public function isResolved()
    {
        return isset($this->regexStr);
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
        $param = $this->regexStr;
        $message = "{\"Type\":\"$className\", \"Name\":\"$name\", \"Param\":\"$param\"}";

        return $message;
    }
}

?>