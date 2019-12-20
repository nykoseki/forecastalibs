<?php

namespace ForecastaTest\Parser\Impl;

use PHPUnit\Framework\TestCase;

use Forecasta\Parser;
use Forecasta\Parser\ParserContext as CTX;

use Forecasta\Parser\ParserFactory;
use Forecasta\Comment\Processor\CommentParser;
use Forecasta\Parser\Impl\JsonParser;
use Forecasta\Parser\Impl\FalseParser;

class AnyParserTestCase extends TestCase
{

    private $parser = null;
    private $clsName = null;

    public function setUp(): void
    {
        $this->parser = new Parser\Impl\ManyParser();
        $this->parser->add(new Parser\Impl\TokenParser("ABC"));
    }

    // =================================================================================================================

    public function testParsed()
{
    $ctx = CTX::create("ABC");

    $result = $this->parser->parse($ctx);
    $this->clsName = get_class($this->parser);

    $parsed = $result->parsed();

    $this->assertEquals(1, count($parsed), "{$this->clsName}#testParsed(Len) : Fail");

    if(count($parsed) === 1) {
        $parsed0 = $parsed[0];
        $this->assertEquals("ABC", $parsed0, "{$this->clsName}#testParsed(1) : Fail");
    }
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


    public function testParsedInvalid()
    {
        $ctx = CTX::create("AB");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $current = $result->parsed();

        $this->assertEquals(0, count($current), "{$this->clsName}#testParsedInvalid : Fail");
    }

    public function testFinishedInvalid() {
        $ctx = CTX::create("AB");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(false, $result->isFinished(), "{$this->clsName}#testFinishedInvalid : Fail");
    }

    public function testResultInvalid() {
        $ctx = CTX::create("AB");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultInvalid : Fail");
    }

    public function testCurrentInvalid() {
        $ctx = CTX::create("AB");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(0, $result->current(), "{$this->clsName}#testCurrentInvalid : Fail");
    }

    // =================================================================================================================


    public function testParsedMany()
    {
        $ctx = CTX::create("ABCABC");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed = $result->parsed();
        $len = count($parsed);
        if($len === 2) {
            $parsed0 = $parsed[0];
            $parsed1 = $parsed[1];

            $this->assertEquals("ABC", $parsed0, "{$this->clsName}#testParsedMany(1) : Fail");
            $this->assertEquals("ABC", $parsed1, "{$this->clsName}#testParsedMany(2) : Fail");
        }
    }

    public function testFinishedMany() {
        $ctx = CTX::create("ABCABC");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedMany : Fail");
    }

    public function testResultMany() {
        $ctx = CTX::create("ABCABC");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultMany : Fail");
    }

    public function testCurrentMany() {
        $ctx = CTX::create("ABCABC");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(6, $result->current(), "{$this->clsName}#testCurrentMany : Fail");
    }
}