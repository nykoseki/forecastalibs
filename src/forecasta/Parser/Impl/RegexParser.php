<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser as P;
use Forecasta\Parser\Impl\ParserTrait as PST;

/**
 * 指定した正規表現文字列にマッチする場合に成功するパーサです
 * @author nkoseki
 *
 */
class RegexParser implements P\Parser
{
    use PST;

    private $regexStr;

    /**
     * パースメソッド
     * @param ParserContext $ctx
     * @return ParserContext コンテキスト
     */
    public function parse($context)
    {
        $this->onTry();
        $target0 = $context->target();
        $position = $context->current();
        $len = mb_strlen($target0);

        $tmpTarget = mb_substr($target0, $position, $len - $position);

        //$tmpRegexp = $this->normalize($this->regexStr);
        $tmpRegexp = $this->regexStr;

        //applLog("RegexParser:target", $tmpTarget);
        preg_match($tmpRegexp, $tmpTarget, $matches);

        //applLog("RegexParser:match", $matches);

        if (count($matches) > 0) {
            $match = $matches[0];

            $matchLen = mb_strlen($match);
            $position = $position + $matchLen;

            //applLog("RegexParser", $matches);
            $this->onSuccess();
            return new P\ParserContext($context->target(), $position, $match, true);
        } else {
            $this->onError();
            return (new P\impl\FalseParser())->parse($context);
        }
    }

    private function normalize($regex)
    {

        preg_match("/\/(.+?)\//", $regex, $matches);

        //applLog("RegexParser", $matches);

        if (count($matches) > 1) {
            $reg = $matches[1];

            $prefix = mb_substr($reg, 0, 1);
            if ($prefix != '^') {

                //applLog("RegexParser", '^'. $reg);
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
        $this->name = 'Anonymous_' . md5(rand());
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
        applLog("outputRecursive", $searched);
        $searched[] = $this->name;

        $className = str_replace("\\", "/", $className);

        $name = $this->name;
        $param = $this->regexStr;
        $message = "{\"Type\":\"$className\", \"Name\":\"$name\", \"Param\":\"$param\"}";

        return $message;
    }
}

?>