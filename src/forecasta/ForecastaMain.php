<?php

namespace Forecasta;

use Forecasta\Parser as P;
use Forecasta\Parser\ParserContext as CTX;
use Forecasta\Parser\Impl as PImpl;
use Forecasta\Parser\ParserFactory;

class ForecastaMain
{
    public function test0001()
    {
        return "test00001-00001";
    }

    public function parse001()
    {
        $token = ParserFactory::Token("abc");

        $result = $token->parse(CTX::create("abcabc"));
        
        return $result;
    }
}