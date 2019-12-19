<?php

namespace ForecastaTest\Parser\Impl;

use PHPUnit\Framework\TestCase;

use Forecasta\Parser;
use Forecasta\Parser\ParserContext as CTX;

use Forecasta\Parser\ParserFactory;
use Forecasta\Comment\Processor\CommentParser;
use Forecasta\Parser\Impl\JsonParser;
use Forecasta\Parser\Impl\FalseParser;

class TokenParserTestCase extends TestCase
{

    private $parser = null;
    private $clsName = null;

    public function setUp(): void
    {
        $this->parser = new Parser\Impl\TokenParser("ABC");
    }

    // =================================================================================================================

    public function testParsed()
    {
        $ctx = CTX::create("ABC");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals("ABC", $result->parsed(), "{$this->clsName}#testParsed : Fail");
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
    public function testParsed02()
    {
        $ctx = CTX::create("ABCx");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals("ABC", $result->parsed(), "{$this->clsName}#testParsed02 : Fail");
    }

    public function testFinished02() {
        $ctx = CTX::create("ABCx");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(false, $result->isFinished(), "{$this->clsName}#testFinished02 : Fail");
    }

    public function testResult02() {
        $ctx = CTX::create("ABCx");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResult02 : Fail");
    }

    public function testCurrent02() {
        $ctx = CTX::create("ABCx");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(3, $result->current(), "{$this->clsName}#testCurrent02 : Fail");
    }

    // =================================================================================================================

    public function testParsedInvalid()
    {
        $ctx = CTX::create("ABX");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(null, $result->parsed(), "{$this->clsName}#testParsedInvalid : Fail");
    }

    public function testFinishedInvalid() {
        $ctx = CTX::create("ABX");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(false, $result->isFinished(), "{$this->clsName}#testFinishedInvalid : Fail");
    }

    public function testResultInvalid() {
        $ctx = CTX::create("ABX");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(false, $result->result(), "{$this->clsName}#testResultInvalid : Fail");
    }

    public function testCurrentInvalid() {
        $ctx = CTX::create("ABX");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(0, $result->current(), "{$this->clsName}#testCurrentInvalid : Fail");
    }
}