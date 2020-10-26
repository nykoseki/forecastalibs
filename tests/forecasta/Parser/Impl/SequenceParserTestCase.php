<?php

namespace ForecastaTest\Parser\Impl;

use PHPUnit\Framework\TestCase;

use Forecasta\Parser;
use Forecasta\Parser\ParserContext;

use Forecasta\Parser\Impl\TokenParser;

class SequenceParserTestCase extends TestCase
{

    private $parser = null;
    private $clsName = null;

    public function setUp(): void
    {
        $this->parser = new Parser\Impl\SequenceParser();

        $this->parser->add(new TokenParser("ABC"));
        $this->parser->add(new TokenParser("-"));
        $this->parser->add(new TokenParser("XYZ"));
    }

    // =================================================================================================================

    public function testParsed()
    {
        $ctx = ParserContext::create("ABC-XYZ");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed = $result->parsed();
        $len = count($parsed);

        $this->assertEquals(3, $len, "{$this->clsName}#testParsed(Length) : Fail");

        if($len === 3) {
            $parsed0 = $parsed[0];
            $parsed1 = $parsed[1];
            $parsed2 = $parsed[2];

            $this->assertEquals("ABC", $parsed0, "{$this->clsName}#testParsed(1) : Fail");
            $this->assertEquals("-", $parsed1, "{$this->clsName}#testParsed(2) : Fail");
            $this->assertEquals("XYZ", $parsed2, "{$this->clsName}#testParsed(3) : Fail");
        }
    }

    public function testFinished() {
        $ctx = ParserContext::create("ABC-XYZ");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinished : Fail");
    }

    public function testResult() {
        $ctx = ParserContext::create("ABC-XYZ");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResult : Fail");
    }

    public function testCurrent() {
        $ctx = ParserContext::create("ABC-XYZ");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(7, $result->current(), "{$this->clsName}#testCurrent : Fail");
    }
}