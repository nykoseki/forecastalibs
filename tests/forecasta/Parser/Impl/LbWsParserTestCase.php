<?php

namespace ForecastaTest\Parser\Impl;

use PHPUnit\Framework\TestCase;

use Forecasta\Parser;
use Forecasta\Parser\ParserContext as CTX;

use Forecasta\Parser\ParserFactory;
use Forecasta\Comment\Processor\CommentParser;
use Forecasta\Parser\Impl\JsonParser;
use Forecasta\Parser\Impl\FalseParser;

class LbWsParserTestCase extends TestCase
{

    private $parser = null;
    private $clsName = null;

    public function setUp(): void
    {
        $this->parser = new Parser\Impl\LbWsParser();
        //$this->parser->add(new Parser\Impl\TokenParser("ABC"));
    }

    // =================================================================================================================

    /**
     * 空白の連続
     */
    public function testParsed01()
    {
        $ctx = CTX::create("     ");

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
        $ctx = CTX::create(" \n  \n \n\n\n ");

        $result = $this->parser->parse($ctx);

        $this->clsName = get_class($this->parser);

        $this->assertEquals("<LbWs>", $result->parsed(), "{$this->clsName}#testParsed : Fail");
    }

    public function testParsed03()
    {
        $tgt = <<<EOF
       
  
   
      
          



EOF;
        $ctx = CTX::create($tgt);

        $result = $this->parser->parse($ctx);

        $this->clsName = get_class($this->parser);

        $this->assertEquals("<LbWs>", $result->parsed(), "{$this->clsName}#testParsed : Fail");
    }

    public function testParsed04()
    {
        $tgt = "\n\n\n\n\n\t\t\n\n\n\n\n";
        $ctx = CTX::create($tgt);

        $result = $this->parser->parse($ctx);

        $this->clsName = get_class($this->parser);

        $this->assertEquals("<LbWs>", $result->parsed(), "{$this->clsName}#testParsed : Fail");
    }

    public function testFinished() {
        $ctx = CTX::create("     ");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinished : Fail");
    }

    public function testResult() {
        $ctx = CTX::create("     ");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResult : Fail");
    }

    public function testCurrent() {
        $ctx = CTX::create("     ");

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(5, $result->current(), "{$this->clsName}#testCurrent : Fail");
    }

    // =================================================================================================================

    public function testFinishedMultiLine() {
        $taregt = <<< EOF
      
      
      
      


      
EOF;

        $ctx = CTX::create($taregt);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedMultiLine : Fail");
    }

    public function testResultMultiLine() {
        $taregt = <<< EOF
      
      
      
      


      
EOF;

        $ctx = CTX::create($taregt);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultMultiLine : Fail");
    }

    public function testCurrentMultiLine() {
        $taregt = <<< EOF
      
      
      
      


      
EOF;

        $ctx = CTX::create($taregt);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $len = mb_strlen($taregt);

        $this->assertEquals($len, $result->current(), "{$this->clsName}#testCurrentMultiLine : Fail");
    }
}