<?php

namespace ForecastaTest\Parser;

use PHPUnit\Framework\TestCase;

use Forecasta\Parser as P;
use Forecasta\Parser\ParserContext as CTX;

use Forecasta\Parser\ParserFactory;
use Forecasta\Comment\Processor\CommentParser;
use Forecasta\Parser\Impl\JsonParser;
use Forecasta\Parser\Impl\FalseParser;

class ParserTestCase extends TestCase
{

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

        $ctx = CTX::create($tgt);
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


        $ctx = CTX::create("{{tu{v}}abc{ijk{op}lmn}def{qr{wx{y}z}s}gh}");

        $result = $expression->parse($ctx);

        $this->assertTrue($result->result(), "CompositeParserSample is Fail!");
    }
}