<?php

namespace Forecasta\Parser\Impl;

use Forecasta\Parser as P;
use Forecasta\Parser\Impl\ParserTrait as PST;

/**
 * 登録されたパーサを遅延処理で提供するパーサコンビネータです
 * @author nkoseki
 *
 */
class ForwardParser implements P\Parser, P\HasMoreChildren
{
    use PST;

    private $forwarder;

    /**
     * パースメソッド
     * @param ParserContext $ctx
     * @return ParserContext コンテキスト
     */
    public function parse($context)
    {
        $this->onTry();

        $parser = $this->forwarder->__invoke();


        return $parser->parse($context);
    }

    /**
     * $parser->forwardのシンタックスシュガー
     * @param P\Parser $parser
     * @return ForwardParser
     */
    public function __invoke(P\Parser $parser) {
        return $this->forward($parser);
    }

    public function forward(P\Parser $parser)
    {
        $this->forwarder = function () use (&$parser) {
            return $parser;
        };

        return $this;
    }

    public function __construct()
    {
        $this->name = 'Anonymous_' . md5(rand());
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

        applLog("outputRecursive", $searched);

        $searched[] = $this->name;

        $name = $this->name;
        $param = '';
        if (!is_null($this->forwarder) && $this->forwarder->__invoke() instanceof P\Parser) {
            $forwardedFor = $this->forwarder->__invoke();

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