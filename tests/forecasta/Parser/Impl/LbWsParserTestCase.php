<?php

namespace ForecastaTest\Parser\Impl;

use PHPUnit\Framework\TestCase;

use Forecasta\Parser\ParserContext;
use Forecasta\Parser\Impl\LbWsParser;

class LbWsParserTestCase extends TestCase
{

    private $parser = null;
    private $clsName = null;

    public function setUp(): void
    {
        $this->parser = new LbWsParser();
        //$this->parser->add(new Parser\Impl\TokenParser("ABC"));
    }

    // =================================================================================================================

    /**
     * 空白の連続
     */
    public function testParsed01()
    {
        $ctx = ParserContext::create("     ");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        //$this->assertTrue(true, "{$this->clsName}#testParsed : Fail");
        $this->assertEquals("<LbWs>", $result->parsed(), "{$this->clsName}#testParsed : Fail");
    }

    /**
     * 空白と改行(\n)の連続
     */
    public function testParsed02()
    {
        $ctx = ParserContext::create(" \n  \n \n\n\n ");

        $result = $this->parser->parse($ctx);

        $this->clsName = get_class($this->parser);

        $this->assertEquals("<LbWs>", $result->parsed(), "{$this->clsName}#testParsed : Fail");
    }

    public function testParsed03()
    {
        $tgt = <<<EOF
       
  
   
      
          



EOF;
        $ctx = ParserContext::create($tgt);

        $result = $this->parser->parse($ctx);

        $this->clsName = get_class($this->parser);

        $this->assertEquals("<LbWs>", $result->parsed(), "{$this->clsName}#testParsed : Fail");
    }

    public function testParsed04()
    {
        $tgt = "\n\n\n\n\n\t\t\n\n\n\n\n";
        $ctx = ParserContext::create($tgt);

        $result = $this->parser->parse($ctx);

        $this->clsName = get_class($this->parser);

        $this->assertEquals("<LbWs>", $result->parsed(), "{$this->clsName}#testParsed : Fail");
    }

    public function testFinished() {
        $ctx = ParserContext::create("     ");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinished : Fail");
    }

    public function testResult() {
        $ctx = ParserContext::create("     ");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResult : Fail");
    }

    public function testCurrent() {
        $ctx = ParserContext::create("     ");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(5, $result->current(), "{$this->clsName}#testCurrent : Fail");
    }

    // =================================================================================================================

    public function testFinishedMultiLine() {
        $taregt = <<< EOF
      
      
      
      


      
EOF;

        $ctx = ParserContext::create($taregt);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedMultiLine : Fail");
    }

    public function testResultMultiLine() {
        $taregt = <<< EOF
      
      
      
      


      
EOF;

        $ctx = ParserContext::create($taregt);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultMultiLine : Fail");
    }

    public function testCurrentMultiLine() {
        $taregt = <<< EOF
      
      
      
      


      
EOF;

        $ctx = ParserContext::create($taregt);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $len = mb_strlen($taregt);

        $this->assertEquals($len, $result->current(), "{$this->clsName}#testCurrentMultiLine : Fail");
    }
}