<?php

namespace ForecastaTest\Parser\Impl;

use PHPUnit\Framework\TestCase;

use Forecasta\Parser;
use Forecasta\Common\ArrayUtil;
use Forecasta\Parser\ParserContext as CTX;

use Forecasta\Parser\ParserFactory;
use Forecasta\Comment\Processor\CommentParser;
use Forecasta\Parser\Impl\JsonParser;
use Forecasta\Parser\Impl\FalseParser;

class BoolParserTestCase extends TestCase
{

    private $parser = null;
    private $clsName = null;

    public function setUp(): void
    {
        $this->parser = new Parser\Impl\BoolParser();
    }

    // =================================================================================================================

    public function testParsed_true()
    {
        $ctx = CTX::create("true");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals("true", $result->parsed(), "{$this->clsName}#testParsed_true : Fail");


    }

    public function testFinished_true() {
        $ctx = CTX::create("true");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinished_true : Fail");
    }

    public function testResult_true() {
        $ctx = CTX::create("true");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResult_true : Fail");
    }

    public function testCurrent_true() {
        $ctx = CTX::create("true");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(4, $result->current(), "{$this->clsName}#testCurrent_true : Fail");
    }

    // =================================================================================================================

    public function testParsed_false()
    {
        $ctx = CTX::create("false");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals("false", $result->parsed(), "{$this->clsName}#testParsed_false : Fail");


    }

    public function testFinished_false() {
        $ctx = CTX::create("false");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinished_false : Fail");
    }

    public function testResult_false() {
        $ctx = CTX::create("false");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResult_false : Fail");
    }

    public function testCurrent_false() {
        $ctx = CTX::create("false");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(5, $result->current(), "{$this->clsName}#testCurrent_false : Fail");
    }


    // =================================================================================================================


    public function testParsed_TR()
    {
        $ctx = CTX::create("TRUE");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals("TRUE", $result->parsed(), "{$this->clsName}#testParsed_TR : Fail");


    }

    public function testFinished_TR() {
        $ctx = CTX::create("TRUE");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinished_TR : Fail");
    }

    public function testResult_TR() {
        $ctx = CTX::create("TRUE");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResult_TR : Fail");
    }

    public function testCurrent_TR() {
        $ctx = CTX::create("TRUE");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(4, $result->current(), "{$this->clsName}#testCurrent_TR : Fail");
    }

    // =================================================================================================================

    public function testParsed_FL()
    {
        $ctx = CTX::create("FALSE");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals("FALSE", $result->parsed(), "{$this->clsName}#testParsed_FL : Fail");
    }

    public function testFinished_FL() {
        $ctx = CTX::create("FALSE");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinished_FL : Fail");
    }

    public function testResult_FL() {
        $ctx = CTX::create("FALSE");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResult_FL : Fail");
    }

    public function testCurrent_FL() {
        $ctx = CTX::create("FALSE");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(5, $result->current(), "{$this->clsName}#testCurrent_FL : Fail");
    }
}