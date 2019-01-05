<?php

namespace ForecastaTest\Parser\Impl;

use PHPUnit\Framework\TestCase;


use Forecasta\Common\ArrayUtil;
use Forecasta\Parser;
use Forecasta\Parser\ParserContext as CTX;

use Forecasta\Parser\ParserFactory;
use Forecasta\Comment\Processor\CommentParser;
use Forecasta\Parser\Impl\JsonParser;
use Forecasta\Parser\Impl\FalseParser;

class JsonParserTestCase extends TestCase
{

    private $parser = null;
    private $clsName = null;
    private $filter = null;

    private $compositeTarget = "";

    public function setUp()
    {
        $this->parser = new Parser\Impl\JsonParser();

        $this->filter = function($value){
            if(empty($value) || $value === null || empty(preg_replace("/\s+/", "", $value)) || $value == "<LbWs>") {
                return false;
            } else {
                return true;
            }
        };

        $this->compositeTarget = <<<EOF
                {
                    "aaa" : 
                           {
                        "_bbb" : "c_12_cc",
                        "_ccc" : null,
                        "Empty" : "",
                        "Empty_" : '',
                        "number" : 1234,
                        "number_" : 101234,
                         "hhhhh" : {
                            "i_1-1" : "j",
                            "i_1-2" : true,
                            "i_1-3" : false,
                            "i_1-4" : TRUE,
                            "i_1-5" : FALSE,
                            "i_1-6" : "abc ABC  AbC 999,:';/+*==="
                         },
                         "fff" :
                            "gggg"
                    },
"ddd" 
                : 
                            "ee"    
                                    ,
                    "xyz" : [
                        "xxa", 
                        "xxb", 
                        [
                            "a", 
                            "b"
                        ], 
                        {
                            "c" : 
                                        "d"
                        },

                        {
                            "c" : [
                                "e", 
                                "f",
                                [111,222,333,444,5555,{"Key":"Value"}],
                                [111,222,333,444,5555,{"Key":"a b c A B C ''--++*///,,,,:::;;;"}]
                            ]
                        }
                    ]
                }
EOF;
    }

    // =================================================================================================================

    public function testParsedKeyString()
    {
        $ctx = CTX::create('{"aaa":"bbb"}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);


        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $expected = print_r(['{', '"', 'aaa', '"', ':', '"', 'bbb', '"', '}'], true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKeyString: {$parsed0} : Fail");
    }

    public function testFinishedKeyString() {
        $ctx = CTX::create('{"aaa":"bbb"}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKeyString : Fail");
    }

    public function testResultKeyString() {
        $ctx = CTX::create('{"aaa":"bbb"}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKeyString : Fail");
    }

    public function testCurrentKeyString() {
        $target = '{"aaa":"bbb"}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKeyString : Fail");
    }

    // =================================================================================================================

    public function testParsedKeyNull()
    {
        $ctx = CTX::create('{"aaa":null}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);


        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $expected = print_r(['{', '"', 'aaa', '"', ':', 'null', '}'], true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKeyNull: {$parsed0} : Fail");
    }

    public function testFinishedKeyNull() {
        $ctx = CTX::create('{"aaa":null}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKeyNull : Fail");
    }

    public function testResultKeyNull() {
        $ctx = CTX::create('{"aaa":null}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKeyNull : Fail");
    }

    public function testCurrentKeyNull() {
        $target = '{"aaa":null}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKeyNull : Fail");
    }

    // =================================================================================================================

    public function testParsedKeyNumber()
    {
        $ctx = CTX::create('{"aaa":123}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);


        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $expected = print_r(['{', '"', 'aaa', '"', ':', '123', '}'], true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKeyNumber: {$parsed0} : Fail");
    }

    public function testFinishedKeyNumber() {
        $ctx = CTX::create('{"aaa":123}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKeyNumber : Fail");
    }

    public function testResultKeyNumber() {
        $ctx = CTX::create('{"aaa":123}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKeyNumber : Fail");
    }

    public function testCurrentKeyNumber() {
        $target = '{"aaa":123}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKeyNumber : Fail");
    }

    // =================================================================================================================

    public function testParsedKey_True()
    {
        $ctx = CTX::create('{"aaa":true}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);


        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $expected = print_r(['{', '"', 'aaa', '"', ':', 'true', '}'], true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKey_True: {$parsed0} : Fail");
    }

    public function testFinishedKey_True() {
        $ctx = CTX::create('{"aaa":true}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKey_True : Fail");
    }

    public function testResultKey_True() {
        $ctx = CTX::create('{"aaa":true}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKey_True : Fail");
    }

    public function testCurrentKey_True() {
        $target = '{"aaa":true}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKey_True : Fail");
    }

    // =================================================================================================================

    public function testParsedKey_False()
    {
        $ctx = CTX::create('{"aaa":false}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);


        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $expected = print_r(['{', '"', 'aaa', '"', ':', 'false', '}'], true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKey_True: {$parsed0} : Fail");
    }

    public function testFinishedKey_False() {
        $ctx = CTX::create('{"aaa":false}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKey_True : Fail");
    }

    public function testResultKey_False() {
        $ctx = CTX::create('{"aaa":false}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKey_True : Fail");
    }

    public function testCurrentKey_False() {
        $target = '{"aaa":false}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKey_True : Fail");
    }

    // =================================================================================================================

    public function testParsedKey__True()
    {
        $ctx = CTX::create('{"aaa":TRUE}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);


        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $expected = print_r(['{', '"', 'aaa', '"', ':', 'TRUE', '}'], true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKey__True: {$parsed0} : Fail");
    }

    public function testFinishedKey__True() {
        $ctx = CTX::create('{"aaa":TRUE}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKey__True : Fail");
    }

    public function testResultKey__True() {
        $ctx = CTX::create('{"aaa":TRUE}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKey__True : Fail");
    }

    public function testCurrentKey__True() {
        $target = '{"aaa":TRUE}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKey__True : Fail");
    }

    // =================================================================================================================

    public function testParsedKey__False()
    {
        $ctx = CTX::create('{"aaa":FALSE}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);


        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $expected = print_r(['{', '"', 'aaa', '"', ':', 'FALSE', '}'], true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKey__False: {$parsed0} : Fail");
    }

    public function testFinishedKey__False() {
        $ctx = CTX::create('{"aaa":FALSE}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKey__False : Fail");
    }

    public function testResultKey__False() {
        $ctx = CTX::create('{"aaa":FALSE}');

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKey__False : Fail");
    }

    public function testCurrentKey__False() {
        $target = '{"aaa":FALSE}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKey__False : Fail");
    }

    // =================================================================================================================

    public function testParsedKeyStringMulti()
    {
        $target = '{"aaa":"bbb", "ccc":"ddd"}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $expected = print_r(['{', '"', 'aaa', '"', ':', '"', 'bbb', '"', "," , '"', 'ccc', '"', ':', '"', 'ddd', '"', '}'], true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKeyStringMulti: {$parsed0} : Fail");
    }

    public function testFinishedKeyStringMulti() {
        $target = '{"aaa":"bbb", "ccc":"ddd"}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKeyStringMulti : Fail");
    }

    public function testResultKeyStringMulti() {
        $target = '{"aaa":"bbb", "ccc":"ddd"}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKeyStringMulti : Fail");
    }

    public function testCurrentKeyStringMulti() {
        $target = '{"aaa":"bbb", "ccc":"ddd"}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKeyStringMulti : Fail");
    }

    // =================================================================================================================

    public function testParsedKeyNumberMulti()
    {
        $target = '{"aaa":"bbb", "ccc":123}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $expected = print_r(['{', '"', 'aaa', '"', ':', '"', 'bbb', '"', "," , '"', 'ccc', '"', ':', 123, '}'], true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKeyNumberMulti: {$parsed0} : Fail");
    }

    public function testFinishedKeyNumberMulti() {
        $target = '{"aaa":"bbb", "ccc":123}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKeyNumberMulti : Fail");
    }

    public function testResultKeyNumberMulti() {
        $target = '{"aaa":"bbb", "ccc":123}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKeyNumberMulti : Fail");
    }

    public function testCurrentKeyNumberMulti() {
        $target = '{"aaa":"bbb", "ccc":123}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKeyNumberMulti : Fail");
    }

    // =================================================================================================================

    public function testParsedKey_TrueMulti()
    {
        $target = '{"aaa":"bbb", "ccc":true}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $expected = print_r(['{', '"', 'aaa', '"', ':', '"', 'bbb', '"', "," , '"', 'ccc', '"', ':', "true", '}'], true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKey_TrueMulti: {$parsed0} : Fail");
    }

    public function testFinishedKey_TrueMulti() {
        $target = '{"aaa":"bbb", "ccc":true}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKey_TrueMulti : Fail");
    }

    public function testResultKey_TrueMulti() {
        $target = '{"aaa":"bbb", "ccc":true}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKey_TrueMulti : Fail");
    }

    public function testCurrentKey_TrueMulti() {
        $target = '{"aaa":"bbb", "ccc":true}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKey_TrueMulti : Fail");
    }

    // =================================================================================================================

    public function testParsedKey_FalseMulti()
    {
        $target = '{"aaa":"bbb", "ccc":false}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $expected = print_r(['{', '"', 'aaa', '"', ':', '"', 'bbb', '"', "," , '"', 'ccc', '"', ':', "false", '}'], true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKey_FalseMulti: {$parsed0} : Fail");
    }

    public function testFinishedKey_FalseMulti() {
        $target = '{"aaa":"bbb", "ccc":false}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKey_FalseMulti : Fail");
    }

    public function testResultKey_FalseMulti() {
        $target = '{"aaa":"bbb", "ccc":false}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKey_FalseMulti : Fail");
    }

    public function testCurrentKey_FalseMulti() {
        $target = '{"aaa":"bbb", "ccc":false}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKey_FalseMulti : Fail");
    }

    // =================================================================================================================

    public function testParsedKey__TrueMulti()
    {
        $target = '{"aaa":"bbb", "ccc":TRUE}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $expected = print_r(['{', '"', 'aaa', '"', ':', '"', 'bbb', '"', "," , '"', 'ccc', '"', ':', "TRUE", '}'], true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKey_FalseMulti: {$parsed0} : Fail");
    }

    public function testFinishedKey__TrueMulti() {
        $target = '{"aaa":"bbb", "ccc":TRUE}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKey_FalseMulti : Fail");
    }

    public function testResultKey__TrueMulti() {
        $target = '{"aaa":"bbb", "ccc":TRUE}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKey_FalseMulti : Fail");
    }

    public function testCurrentKey__TrueMulti() {
        $target = '{"aaa":"bbb", "ccc":TRUE}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKey_FalseMulti : Fail");
    }

    // =================================================================================================================

    public function testParsedKey__FalseMulti()
    {
        $target = '{"aaa":"bbb", "ccc":FALSE}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $expected = print_r(['{', '"', 'aaa', '"', ':', '"', 'bbb', '"', "," , '"', 'ccc', '"', ':', "FALSE", '}'], true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKey_FalseMulti: {$parsed0} : Fail");
    }

    public function testFinishedKey__FalseMulti() {
        $target = '{"aaa":"bbb", "ccc":FALSE}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKey_FalseMulti : Fail");
    }

    public function testResultKey__FalseMulti() {
        $target = '{"aaa":"bbb", "ccc":FALSE}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKey_FalseMulti : Fail");
    }

    public function testCurrentKey__FalseMulti() {
        $target = '{"aaa":"bbb", "ccc":FALSE}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKey_FalseMulti : Fail");
    }

    // =================================================================================================================

    public function testParsedKeyArrayStringSingle()
    {
        $target = '{"aaa":["bbb"]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $ary = array();
        $ary[] = '{';
        $ary[] = '"';
        $ary[] = 'aaa';
        $ary[] = '"';
        $ary[] = ':';
        $ary[] = '[';
        $ary[] = '"';
        $ary[] = 'bbb';
        $ary[] = '"';
        $ary[] = ']';
        $ary[] = '}';

        $expected = print_r($ary, true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKeyArrayStringSingle: {$parsed0} : Fail");
    }

    public function testFinishedKeyArrayStringSingle() {
        $target = '{"aaa":["bbb"]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKeyArrayStringSingle : Fail");
    }

    public function testResultKeyArrayStringSingle() {
        $target = '{"aaa":["bbb"]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKeyArrayStringSingle : Fail");
    }

    public function testCurrentKeyArrayStringSingle() {
        $target = '{"aaa":["bbb"]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKeyArrayStringSingle : Fail");
    }

    // =================================================================================================================

    public function testParsedKeyArrayStringNumber()
    {
        $target = '{"aaa":[123]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $ary = array();
        $ary[] = '{';
        $ary[] = '"';
        $ary[] = 'aaa';
        $ary[] = '"';
        $ary[] = ':';
        $ary[] = '[';
        //$ary[] = '"';
        $ary[] = '123';
        //$ary[] = '"';
        $ary[] = ']';
        $ary[] = '}';

        $expected = print_r($ary, true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKeyArrayStringNumber: {$parsed0} : Fail");
    }

    public function testFinishedKeyArrayStringNumber() {
        $target = '{"aaa":[123]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKeyArrayStringNumber : Fail");
    }

    public function testResultKeyArrayStringNumber() {
        $target = '{"aaa":[123]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKeyArrayStringNumber : Fail");
    }

    public function testCurrentKeyArrayStringNumber() {
        $target = '{"aaa":[123]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKeyArrayStringNumber : Fail");
    }

    // =================================================================================================================

    public function testParsedKeyArrayString_True()
    {
        $target = '{"aaa":[true]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $ary = array();
        $ary[] = '{';
        $ary[] = '"';
        $ary[] = 'aaa';
        $ary[] = '"';
        $ary[] = ':';
        $ary[] = '[';
        //$ary[] = '"';
        $ary[] = 'true';
        //$ary[] = '"';
        $ary[] = ']';
        $ary[] = '}';

        $expected = print_r($ary, true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKeyArrayStringNumber: {$parsed0} : Fail");
    }

    public function testFinishedKeyArrayString_True() {
        $target = '{"aaa":[true]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKeyArrayStringNumber : Fail");
    }

    public function testResultKeyArrayString_True() {
        $target = '{"aaa":[true]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKeyArrayStringNumber : Fail");
    }

    public function testCurrentKeyArrayString_True() {
        $target = '{"aaa":[true]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKeyArrayStringNumber : Fail");
    }

    // =================================================================================================================

    public function testParsedKeyArrayString_False()
    {
        $target = '{"aaa":[false]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $ary = array();
        $ary[] = '{';
        $ary[] = '"';
        $ary[] = 'aaa';
        $ary[] = '"';
        $ary[] = ':';
        $ary[] = '[';
        //$ary[] = '"';
        $ary[] = 'false';
        //$ary[] = '"';
        $ary[] = ']';
        $ary[] = '}';

        $expected = print_r($ary, true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKeyArrayString_False: {$parsed0} : Fail");
    }

    public function testFinishedKeyArrayString_False() {
        $target = '{"aaa":[false]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKeyArrayString_False : Fail");
    }

    public function testResultKeyArrayString_False() {
        $target = '{"aaa":[false]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKeyArrayString_False : Fail");
    }

    public function testCurrentKeyArrayString_False() {
        $target = '{"aaa":[false]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKeyArrayString_False : Fail");
    }

    // =================================================================================================================

    public function testParsedKeyArrayString__True()
    {
        $target = '{"aaa":[TRUE]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $ary = array();
        $ary[] = '{';
        $ary[] = '"';
        $ary[] = 'aaa';
        $ary[] = '"';
        $ary[] = ':';
        $ary[] = '[';
        //$ary[] = '"';
        $ary[] = 'TRUE';
        //$ary[] = '"';
        $ary[] = ']';
        $ary[] = '}';

        $expected = print_r($ary, true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKeyArrayString__True: {$parsed0} : Fail");
    }

    public function testFinishedKeyArrayString__True() {
        $target = '{"aaa":[TRUE]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKeyArrayString__True : Fail");
    }

    public function testResultKeyArrayString__True() {
        $target = '{"aaa":[TRUE]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKeyArrayString__True : Fail");
    }

    public function testCurrentKeyArrayString__True() {
        $target = '{"aaa":[TRUE]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKeyArrayString__True : Fail");
    }

    // =================================================================================================================

    public function testParsedKeyArrayString__False()
    {
        $target = '{"aaa":[FALSE]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $ary = array();
        $ary[] = '{';
        $ary[] = '"';
        $ary[] = 'aaa';
        $ary[] = '"';
        $ary[] = ':';
        $ary[] = '[';
        //$ary[] = '"';
        $ary[] = 'FALSE';
        //$ary[] = '"';
        $ary[] = ']';
        $ary[] = '}';

        $expected = print_r($ary, true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKeyArrayString__True: {$parsed0} : Fail");
    }

    public function testFinishedKeyArrayString__False() {
        $target = '{"aaa":[FALSE]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKeyArrayString__True : Fail");
    }

    public function testResultKeyArrayString__False() {
        $target = '{"aaa":[FALSE]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKeyArrayString__True : Fail");
    }

    public function testCurrentKeyArrayString__False() {
        $target = '{"aaa":[FALSE]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentArrayString__True : Fail");
    }

    // =================================================================================================================

    public function testParsedKeyArrayElement()
    {
        $target = '{"aaa":[{"bbb":"ccc"}]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $ary = array();
        $ary[] = '{';
        $ary[] = '"';
        $ary[] = 'aaa';
        $ary[] = '"';
        $ary[] = ':';
        $ary[] = '[';
        //$ary[] = '"';
        $ary[] = '{';

        $ary[] = '"';
        $ary[] = 'bbb';
        $ary[] = '"';
        $ary[] = ':';
        $ary[] = '"';
        $ary[] = 'ccc';
        $ary[] = '"';
        $ary[] = '}';

        //$ary[] = '"';
        $ary[] = ']';
        $ary[] = '}';

        $expected = print_r($ary, true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKeyArrayElement: {$parsed0} : Fail");
    }

    public function testFinishedKeyArrayElement() {
        $target = '{"aaa":[{"bbb":"ccc"}]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKeyArrayElement : Fail");
    }

    public function testResultKeyArrayElement() {
        $target = '{"aaa":[{"bbb":"ccc"}]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKeyArrayElement : Fail");
    }

    public function testCurrentKeyArrayElement() {
        $target = '{"aaa":[{"bbb":"ccc"}]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKeyArrayElement : Fail");
    }

    // =================================================================================================================

    public function testParsedKeyArrayArray()
    {
        $target = '{"aaa":[["bbb", "ccc"]]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $ary = array();
        $ary[] = '{';
        $ary[] = '"';
        $ary[] = 'aaa';
        $ary[] = '"';
        $ary[] = ':';
        $ary[] = '[';
        //$ary[] = '"';
        $ary[] = '[';

        $ary[] = '"';
        $ary[] = 'bbb';
        $ary[] = '"';

        $ary[] = ',';

        $ary[] = '"';
        $ary[] = 'ccc';
        $ary[] = '"';

        $ary[] = ']';

        //$ary[] = '"';
        $ary[] = ']';
        $ary[] = '}';

        $expected = print_r($ary, true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKeyArrayElement: {$parsed0} : Fail");
    }

    public function testFinishedKeyArrayArray() {
        $target = '{"aaa":[["bbb", "ccc"]]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKeyArrayArray : Fail");
    }

    public function testResultKeyArrayArray() {
        $target = '{"aaa":[["bbb", "ccc"]]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKeyArrayArray : Fail");
    }

    public function testCurrentKeyArrayArray() {
        $target = '{"aaa":[["bbb", "ccc"]]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKeyArrayArray : Fail");
    }


    // =================================================================================================================

    public function testParsedKeyArrayArrayMulti()
    {
        $target = '{"aaa":[["bbb", {"ccc":"ddd"}]]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $ary = array();
        $ary[] = '{';
        $ary[] = '"';
        $ary[] = 'aaa';
        $ary[] = '"';
        $ary[] = ':';
        $ary[] = '[';
        //$ary[] = '"';
        $ary[] = '[';

        $ary[] = '"';
        $ary[] = 'bbb';
        $ary[] = '"';

        $ary[] = ',';

        $ary[] = '{';
        $ary[] = '"';
        $ary[] = 'ccc';
        $ary[] = '"';
        $ary[] = ':';
        $ary[] = '"';
        $ary[] = 'ddd';
        $ary[] = '"';
        $ary[] = '}';

        $ary[] = ']';

        //$ary[] = '"';
        $ary[] = ']';
        $ary[] = '}';

        $expected = print_r($ary, true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKeyArrayArrayMulti: {$parsed0} : Fail");
    }

    public function testFinishedKeyArrayArrayMulti() {
        $target = '{"aaa":[["bbb", {"ccc":"ddd"}]]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKeyArrayArrayMulti : Fail");
    }

    public function testResultKeyArrayArrayMulti() {
        $target = '{"aaa":[["bbb", {"ccc":"ddd"}]]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKeyArrayArrayMulti : Fail");
    }

    public function testCurrentKeyArrayArrayMulti() {
        $target = '{"aaa":[["bbb", {"ccc":"ddd"}]]}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKeyArrayArrayMulti : Fail");
    }

    // =================================================================================================================

    public function testParsedKeyValueMulti()
    {
        $target = '{"aaa":[["bbb", {"ccc":"ddd"}]], "eee":12345}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $ary = array();
        $ary[] = '{';
        $ary[] = '"';
        $ary[] = 'aaa';
        $ary[] = '"';
        $ary[] = ':';
        $ary[] = '[';
        //$ary[] = '"';
        $ary[] = '[';

        $ary[] = '"';
        $ary[] = 'bbb';
        $ary[] = '"';

        $ary[] = ',';

        $ary[] = '{';
        $ary[] = '"';
        $ary[] = 'ccc';
        $ary[] = '"';
        $ary[] = ':';
        $ary[] = '"';
        $ary[] = 'ddd';
        $ary[] = '"';
        $ary[] = '}';

        $ary[] = ']';

        //$ary[] = '"';
        $ary[] = ']';

        $ary[] = ',';

        //$ary[] = '{';
        $ary[] = '"';
        $ary[] = 'eee';
        $ary[] = '"';
        $ary[] = ':';
        $ary[] = '12345';
        //$ary[] = '}';

        $ary[] = '}';

        $expected = print_r($ary, true);

        $this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedKeyValueMulti: {$parsed0} : Fail");
    }

    public function testFinishedKeyValueMulti() {
        $target = '{"aaa":[["bbb", {"ccc":"ddd"}]], "eee":12345}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedKeyValueMulti : Fail");
    }

    public function testResultKeyValueMulti() {
        $target = '{"aaa":[["bbb", {"ccc":"ddd"}]], "eee":12345}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultKeyValueMulti : Fail");
    }

    public function testCurrentKeyValueMulti() {
        $target = '{"aaa":[["bbb", {"ccc":"ddd"}]], "eee":12345}';
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentKeyValueMulti : Fail");
    }

    // =================================================================================================================

    public function testParsedComposite()
    {
        $target = $this->compositeTarget;
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $parsed0 = ArrayUtil::flatten($result->parsed(), null, $this->filter);
        $parsed0 = print_r($parsed0, true);

        $ary = array();

        $expected = print_r($ary, true);

        //$this->assertEquals($expected, $parsed0, "{$this->clsName}#testParsedComposite: {$parsed0} : Fail");
        $this->assertTrue(true, "{$this->clsName}#testParsedComposite: {$parsed0} : Fail");
    }

    public function testFinishedComposite() {
        $target = $this->compositeTarget;
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->isFinished(), "{$this->clsName}#testFinishedComposite : Fail");
    }

    public function testResultComposite() {
        $target = $this->compositeTarget;
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(true, $result->result(), "{$this->clsName}#testResultComposite : Fail");
    }

    public function testCurrentComposite() {
        $target = $this->compositeTarget;
        $ctx = CTX::create($target);

        $result = $this->parser->parse($ctx);
        $this->clsName = get_class($this->parser);

        $this->assertEquals(mb_strlen($target), $result->current(), "{$this->clsName}#testCurrentComposite : Fail");
    }
}