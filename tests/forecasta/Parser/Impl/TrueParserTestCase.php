<?php

namespace ForecastaTest\Parser\Impl;

use PHPUnit\Framework\TestCase;

use Forecasta\Parser;
use Forecasta\Parser\ParserContext as CTX;

use Forecasta\Parser\ParserFactory;
use Forecasta\Comment\Processor\CommentParser;
use Forecasta\Parser\Impl\JsonParser;
use Forecasta\Parser\Impl\FalseParser;

class TrueParserTestCase extends TestCase
{

    private $parser = null;
    private $clsName = null;

    public function setUp(): void
    {
        $this->parser = new Parser\Impl\TrueParser;
    }

    public function testParsed()
    {
        $ctx = CTX::create("test");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(null, $result->parsed(), "{$this->clsName}#testParsed : Fail");
    }

    public function testFinished() {
        $ctx = CTX::create("test");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(false, $result->isFinished(), "{$this->clsName}#testFinished : Fail");
    }

    public function testResult() {
        $ctx = CTX::create("test");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResult : Fail");
    }

    public function testCurrent() {
        $ctx = CTX::create("test");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(0, $result->current(), "{$this->clsName}#testCurrent : Fail");
    }
}