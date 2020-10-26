<?php

namespace ForecastaTest\Parser;

use PHPUnit\Framework\TestCase;

//use Forecasta\Parser as P;
use Forecasta\Parser\ParserContext;

use Forecasta\Parser\ParserFactory;
//use Forecasta\Comment\Processor\CommentParser;
use Forecasta\Parser\Impl\JsonParser;
//use Forecasta\Parser\Impl\FalseParser;

class ParserTestCase extends TestCase
{

    public function testTrueParser()
    {
        //$open = ParserFactory::Token("{");
        // 数値
        $parser = ParserFactory::True()->setName("W");

        $ctx = ParserContext::create("{{tu{v}}abc{ijk{op}lmn}def{qr{wx{y}z}s}gh}");

        $result = $parser->parse($ctx);

        $this->assertTrue($result->result(), "testTrueParser is Fail!");
    }

    public function testFalseParser()
    {
        //$open = ParserFactory::Token("{");
        // 数値
        $parser = ParserFactory::False()->setName("W");

        $ctx = ParserContext::create("");

        $result = $parser->parse($ctx);

        $this->assertTrue(!$result->result(), "testFalseParser is Fail!");
    }

    public function testCharParser()
    {
        //$open = ParserFactory::Token("{");
        // 数値
        $parser = ParserFactory::Char("X")->setName("W");

        $ctx = ParserContext::create("X");

        $result = $parser->parse($ctx);

        $this->assertTrue($result->result(), "testCharParser is Fail!");
    }

    public function testTokenParser()
    {
        //$open = ParserFactory::Token("{");
        // 数値
        $parser = ParserFactory::Token("123ABC")->setName("W");

        $ctx = ParserContext::create("123ABC");

        $result = $parser->parse($ctx);

        $this->assertTrue($result->result(), "testTokenParser is Fail!");
    }

    public function testRegexParser()
    {
        //$open = ParserFactory::Token("{");
        // 数値
        $parser = ParserFactory::Regex("/^[A-Za-z0-9]+/")->setName("W");

        $ctx = ParserContext::create("123ABCaadfdsafsaefagEfaef9");

        $result = $parser->parse($ctx);

        $this->assertTrue($result->result(), "testRegexParser is Fail!(1)");

        $parser = ParserFactory::Regex("/^[A-Za-z0-9]+/")->setName("W");

        $ctx2 = ParserContext::create("123ABCaadfdsafsaefagEfaef9+_");

        $result = $parser->parse($ctx2);

        $this->assertTrue($result->result(), "testRegexParser is Fail!(2)");


        $parser = ParserFactory::Regex("/^[A-Za-z0-9]+$/")->setName("W");

        $ctx2 = ParserContext::create("123ABCaadfdsafsaefagEfaef9@");

        $result = $parser->parse($ctx2);

        $this->assertTrue(!$result->result(), "testRegexParser is Fail!(3)");
    }

    public function testSeqParser()
    {
        //$open = ParserFactory::Token("{");
        // 数値
        $parser = ParserFactory::Seq()->setName("W");
        $parser->add(ParserFactory::Token("AA"));
        $parser->add(ParserFactory::Token("BB"));
        $parser->add(ParserFactory::Token("CCC"));

        $ctx = ParserContext::create("AABBCCC");

        $result = $parser->parse($ctx);

        $this->assertTrue($result->result(), "testSeqParser is Fail!(1)");

        $ctx = ParserContext::create("AABBCCCz");

        $result = $parser->parse($ctx);

        $resStr = join("", $result->parsed());
        $this->assertTrue($result->result(), "testSeqParser is Fail!(2):{$resStr}");

        $ctx = ParserContext::create("AABBzCCCz");

        $result = $parser->parse($ctx);

        //$resStr = join("", $result->parsed());
        $this->assertTrue(!$result->result(), "testSeqParser is Fail!(2):{$resStr}");
    }

    public function testManyParser()
    {
        //$open = ParserFactory::Token("{");
        // 数値
        $parser = ParserFactory::Many()->setName("W");
        $parser->add(ParserFactory::Token("AAA"));

        $ctx = ParserContext::create("AAA");

        $result = $parser->parse($ctx);

        $this->assertTrue($result->result(), "testManyParser is Fail!(1)");

        $ctx = ParserContext::create("AA");

        $result = $parser->parse($ctx);

        $this->assertTrue($result->result(), "testManyParser is Fail!(2)");

        $ctx = ParserContext::create("");

        $result = $parser->parse($ctx);

        $this->assertTrue($result->result(), "testManyParser is Fail!(3)");
        $parsed = $result->parsed();
        $parsed = join("", $parsed);
        $this->assertTrue($parsed === "", "testManyParser is Fail!(4): '{$parsed}'");
    }

    public function testAnyParser()
    {
        //$open = ParserFactory::Token("{");
        // 数値
        $parser = ParserFactory::Any()->setName("W");
        $parser->add(ParserFactory::Token("AAA"));

        $ctx = ParserContext::create("AAA");

        $result = $parser->parse($ctx);

        $this->assertTrue($result->result(), "testAnyParser is Fail!(1)");

        $ctx = ParserContext::create("AAAAAA");

        $result = $parser->parse($ctx);

        $this->assertTrue($result->result(), "testAnyParser is Fail!(2)");

        $ctx = ParserContext::create("AAAAAAB");

        $result = $parser->parse($ctx);
        $parsed = $result->parsed();
        $parsed = join("", $parsed);
        $this->assertTrue($parsed !== $ctx->target(), "testAnyParser is Fail!(3):{$parsed}");

        $ctx = ParserContext::create("");

        $result = $parser->parse($ctx);

        $this->assertTrue(!$result->result(), "testAnyParser is Fail!(4)");
    }


    public function testChoiceParser()
    {
        //$open = ParserFactory::Token("{");
        // 数値
        $parser = ParserFactory::Choice()->setName("W");
        $parser->add(ParserFactory::Token("XX"));
        $parser->add(ParserFactory::Token("YY"));

        $ctx = ParserContext::create("XX");

        $result = $parser->parse($ctx);

        $this->assertTrue($result->result(), "testChoiceParser is Fail!(1)");

        $ctx = ParserContext::create("YY");

        $result = $parser->parse($ctx);

        $this->assertTrue($result->result(), "testChoiceParser is Fail!(2)");

        $ctx = ParserContext::create("ZZ");

        $result = $parser->parse($ctx);

        $this->assertTrue(!$result->result(), "testChoiceParser is Fail!(3)");
    }

    public function testOptionParser()
    {
        //$open = ParserFactory::Token("{");
        // 数値
        $parser = ParserFactory::Option()->setName("W");
        $parser->add(ParserFactory::Token("XX"));


        $ctx = ParserContext::create("XX");

        $result = $parser->parse($ctx);

        $this->assertTrue($result->result(), "testOptionParser is Fail!(1):{$result->parsed()}");

        $ctx = ParserContext::create("XXXX");

        $result = $parser->parse($ctx);

        $this->assertTrue($result->parsed() === "XX", "testOptionParser is Fail!(2):{$result->parsed()}");

        $ctx = ParserContext::create("YY");

        $result = $parser->parse($ctx);

        $this->assertTrue($result->result(), "testOptionParser is Fail!(2):{$result->parsed()}");
        $this->assertTrue($result->parsed() === null, "testOptionParser is Fail!(3):{$result->parsed()}");
    }

    /**
     * JsonParserのパースが成功するかテストする
     */
    public function testJsonParser()
    {
        $element = (new JsonParser);

        $tgt = <<<EOF
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

        $ctx = ParserContext::create($tgt);
        $result = $element->parse($ctx);

        $this->assertTrue($result->result(), "JsonParser is Fail!");
    }

    /**
     * ForwardParserを用いた再帰的構文解析が成功するかテストする
     */
    public function testCompositeParser()
    {
        //$open = ParserFactory::Token("{");
        // 数値
        $word = ParserFactory::Regex("/^[A-Za-z]+|^[A-Za-z_][A-Za-z0-9]+/")->setName("W");

        // ２項演算子
        $operator = ParserFactory::Token("=>")->setName("Operator");

        // 括弧式
        $parenthesis = ParserFactory::Forward()->setName("Parenthesis");

        // atom := 数値　または　括弧式
        $atom = ParserFactory::Choice()->setName("Atom")->add($word)->add($parenthesis);

        // Expression := atom + (２項演算子＋atom)*
        $expression = ParserFactory::Seq()->setName("Expression")
            ->add($atom)
            ->add(
                ParserFactory::Many()
                    ->add(
                        ParserFactory::Seq()
                            //->add($operator)
                            ->add($atom)
                    )
            );

        // 括弧式 := "{" + Expression + "}"
        $parenthesis->forward(
            ParserFactory::Seq()
                ->add(ParserFactory::Token("{")/*->setName("Left")*/)
                ->add($expression)
                ->add(ParserFactory::Token("}")/*->setName("Right")*/)
        );


        $ctx = ParserContext::create("{{tu{v}}abc{ijk{op}lmn}def{qr{wx{y}z}s}gh}");

        $result = $expression->parse($ctx);

        $this->assertTrue($result->result(), "CompositeParserSample is Fail!");
    }
}