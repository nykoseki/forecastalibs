<?php

namespace ForecastaTest\Parser\Impl;

use PHPUnit\Framework\TestCase;

use Forecasta\Parser;
use Forecasta\Parser\ParserContext as CTX;

use Forecasta\Parser\ParserFactory;
use Forecasta\Comment\Processor\CommentParser;
use Forecasta\Parser\Impl\JsonParser;
use Forecasta\Parser\Impl\FalseParser;

class ChoiceParserTestCase extends TestCase
{

    private $parser = null;
    private $clsName = null;

    public function setUp(): void
    {
        $this->parser = new Parser\Impl\ChoiceParser();
        $this->parser->add(new Parser\Impl\TokenParser("ABC"));
        $this->parser->add(new Parser\Impl\TokenParser("XYZ"));
    }

    // =================================================================================================================

    public function testParsed()
    {
        $ctx = CTX::create("ABC");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed = $result->parsed();
        $this->assertEquals("ABC", $parsed, "{$this->clsName}#testParsed : Fail");
    }

    public function testFinished() {
        $ctx = CTX::create("ABC");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinished : Fail");
    }

    public function testResult() {
        $ctx = CTX::create("ABC");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResult : Fail");
    }

    public function testCurrent() {
        $ctx = CTX::create("ABC");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(3, $result->current(), "{$this->clsName}#testCurrent : Fail");
    }

    // =================================================================================================================

    public function testParsed2()
    {
        $ctx = CTX::create("XYZ");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed = $result->parsed();
        $this->assertEquals("XYZ", $parsed, "{$this->clsName}#testParsed : Fail");
    }

    public function testFinished2() {
        $ctx = CTX::create("XYZ");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinished2 : Fail");
    }

    public function testResult2() {
        $ctx = CTX::create("XYZ");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResult2 : Fail");
    }

    public function testCurrent2() {
        $ctx = CTX::create("XYZ");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(3, $result->current(), "{$this->clsName}#testCurrent2 : Fail");
    }

    // =================================================================================================================

    public function testParsedInvalid()
    {
        $ctx = CTX::create("abc");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed = $result->parsed();
        $this->assertEquals(null, $parsed, "{$this->clsName}#testParsedInvalid : Fail");
    }

    public function testFinishedInvalid() {
        $ctx = CTX::create("abc");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(false, $result->isFinished(), "{$this->clsName}#testFinishedInvalid : Fail");
    }

    public function testResultInvalid() {
        $ctx = CTX::create("abc");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(false, $result->result(), "{$this->clsName}#testResultInvalid : Fail");
    }

    public function testCurrentInvalid() {
        $ctx = CTX::create("abc");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(0, $result->current(), "{$this->clsName}#testCurrentInvalid : Fail");
    }
}