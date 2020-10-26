<?php

namespace ForecastaTest\Parser\Impl;

use Forecasta\Common\ArrayUtil;
use PHPUnit\Framework\TestCase;

use Forecasta\Parser\ParserContext;
use Forecasta\Parser\Impl\EmptyParser;

class EmptyParserTestCase extends TestCase
{

    private $parser = null;
    private $clsName = null;


    private $filter = null;

    public function setUp(): void
    {
        $this->parser = new EmptyParser;

        $this->filter = function($value){
            if(empty($value) || $value === null || empty(preg_replace("/\s+/", "", $value)) || $value == "<LbWs>") {
                return false;
            } else {
                return true;
            }
        };
    }

    public function testParsed()
    {
        $ctx = ParserContext::create("\"\"");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);


        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $expected = print_r([], true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsed : Fail: {$parsed0}");
    }

    public function testFinished() {
        $ctx = ParserContext::create("\"\"");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinished : Fail");
    }

    public function testResult() {
        $ctx = ParserContext::create("\"\"");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResult : Fail");
    }

    public function testCurrent() {
        $ctx = ParserContext::create("\"\"");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(2, $result->current(), "{$this->clsName}#testCurrent : Fail");
    }
}