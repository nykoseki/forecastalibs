<?php

namespace ForecastaTest\Parser\Impl;

use PHPUnit\Framework\TestCase;

use Forecasta\Parser;
use Forecasta\Parser\ParserContext as CTX;

use Forecasta\Parser\ParserFactory;
use Forecasta\Comment\Processor\CommentParser;
use Forecasta\Parser\Impl\JsonParser;
use Forecasta\Parser\Impl\FalseParser;

class ForwardParserTestCase extends TestCase
{

    private $parser = null;
    private $clsName = null;

    public function setUp(): void
    {
        $this->parser = new Parser\Impl\ForwardParser();

        $inner = new Parser\Impl\SequenceParser();
        $inner->add(new Parser\Impl\TokenParser("XXX"));
        $inner->add(
            (new Parser\Impl\OptionParser())
                ->add($this->parser)
        );
        $inner->add(new Parser\Impl\TokenParser("YYY"));

        $this->parser->forward($inner);
    }

    // =================================================================================================================

    public function testParsed()
    {
        $ctx = CTX::create("XXXXXXYYYYYY");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed = $result->parsed();

        if(count($parsed) === 3) {
            $parsed0 = $parsed[0];
            $parsed1 = $parsed[1];
            $parsed2 = $parsed[2];

            $this->assertEquals("XXX", $parsed0, "{$this->clsName}#testParsed(1) : Fail");

            if(count($parsed1) === 3) {
                $parsed10 = $parsed1[0];
                $parsed11 = $parsed1[1];
                $parsed12 = $parsed1[2];

                $this->assertEquals("XXX", $parsed10, "{$this->clsName}#testParsed(2-1) : Fail");
                $this->assertEquals(null, $parsed11, "{$this->clsName}#testParsed(2-2) : Fail");
                $this->assertEquals("YYY", $parsed12, "{$this->clsName}#testParsed(2-3) : Fail");
            } else {
                $this->assertTrue(false, "{$this->clsName}#testParsed(2) : Fail");
            }


            $this->assertEquals("YYY", $parsed2, "{$this->clsName}#testParsed(3) : Fail");
        }

        //$this->assertEquals("XXXXXXYYYYYY", $result->parsed(), "{$this->clsName}#testParsed : Fail");
    }

    public function testFinished() {
        $ctx = CTX::create("XXXXXXYYYYYY");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinished : Fail");
    }

    public function testResult() {
        $ctx = CTX::create("XXXXXXYYYYYY");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResult : Fail");
    }

    public function testCurrent() {
        $ctx = CTX::create("XXXXXXYYYYYY");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(12, $result->current(), "{$this->clsName}#testCurrent : Fail");
    }
}