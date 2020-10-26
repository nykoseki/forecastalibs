<?php
/**
 * Created by PhpStorm.
 * User: nykos
 * Date: 2019/12/20
 * Time: 23:32
 */

namespace ForecastaTest\Parser\Impl;

use PHPUnit\Framework\TestCase;

use Forecasta\Parser\ParserContext;
use Forecasta\Parser\Impl\KeyPairParser;

class KeyPairParserTestCase extends TestCase
{
    private $parser = null;
    private $clsName = null;

    public function setUp(): void
    {
        $this->parser = new KeyPairParser();
        //$this->parser->add(new Parser\Impl\TokenParser("ABC"));
    }

    // =================================================================================================================

    public function testParsed()
    {
        $target = <<<EOF
"aaa"=>


                    (
    "b\bb" =>                  "cc'c'",
    "ddd"
     
     
     
     =>           
    
    
    "eee",
    "fff" => (
        "ggg"   => "hhh",
        "iii"   => "jjj",
        "kkk"   => (
            "lll" => "mmm",
            "nnn" => (
                "rrr" => "sss"
            )
        ),
        "ppp"   => "qqq"
    )
   )
EOF;

        $target = <<<EOF
"ccc" => 
  (
   "ddd" => "eee",
   "fff" => "gggg",
   "hhh" => (
     "iii" => "jjj",
     "kkk" => "lll"
   )
)
EOF;

        $ctx = ParserContext::create($target);

        $result = $this->parser->parse($ctx);
        //$this->clsName = get_class($this->parser);

        $parsed = $result->parsed();
        //$parsed = $result->normalize($parsed);
        /*
        $this->assertEquals(1, count($parsed), "{$this->clsName}#testParsed(Len) : Fail");

        if(count($parsed) === 1) {
            $parsed0 = $parsed[0];
            $this->assertEquals("ABC", $parsed0, "{$this->clsName}#testParsed(1) : Fail");
        }
        */

        $this->assertTrue($result->isFinished(), $result. '');
    }
}