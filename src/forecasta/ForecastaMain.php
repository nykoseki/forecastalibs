<?php

namespace Forecasta;

use Forecasta\Parser as P;
use Forecasta\Parser\ParserContext as CTX;
use Forecasta\Parser\Impl as PImpl;
use Forecasta\Parser\ParserFactory;
use Forecasta\Comment\Processor\CommentParser;

class ForecastaMain
{
    public function test0001()
    {
        return "test00001-00001テスト2";
    }

    public function parse001()
    {
        $token = ParserFactory::Token("abcabc");

        $result = $token->parse(CTX::create("abcabc"));

        return $result;
    }

    public function parse002()
    {
        echo "parse002\n";
        $parser = (new CommentParser)->getTest();

        $ctx = CTX::create('@aaa "xyz"');

        $result = $parser->parse($ctx);

        return $result;
    }

    public function parse003()
    {
        echo "parse003\n";
        $parser = (new CommentParser)->getTest();

        $ctx = CTX::create('@aaa xyz');

        $result = $parser->parse($ctx);

        return $result;
    }

    public function parse004()
    {
        echo "parse004\n";
        $parser = (new CommentParser)->getTest();

        $ctx = CTX::create("@aaa 'xyz'");

        $result = $parser->parse($ctx);

        return $result;
    }


    public function parse005()
    {
        echo "=========================================================================\n";
        echo "parse005\n";
        $camma = ParserFactory::Option()->add(
            ParserFactory::Seq()->add(
                ParserFactory::Regex('/,/')
            )->add(
                ParserFactory::Option()->add(
                    ParserFactory::Regex('/\s+/')
                )
            )
        );

        /*
        $line = ParserFactory::Seq()->add(
            ParserFactory::Regex('/[^,]+/')
        )->add($camma);
        */

        $v = ParserFactory::Regex('/[^,]+/');
        $v->setName("Context");

        $choice = ParserFactory::Choice()->add(
            ParserFactory::Seq()->add(
                $v
            )->add($camma)
        )->add(
            $v
        );

        $many = ParserFactory::Any()->add($choice);

        $ctx = CTX::create("abcde");

        $line = $many;

        $result = $line->parse($ctx);

        echo print_r($result . "", true) . "\n";

        echo "parse006\n";
        $ctx = CTX::create("abcde,");

        $result = $line->parse($ctx);

        echo print_r($result . "", true) . "\n";

        echo "parse007\n";
        $ctx = CTX::create("abcde,  ");

        $result = $line->parse($ctx);

        echo print_r($result . "", true) . "\n";

        echo "parse008\n";
        $ctx = CTX::create("abcde,      ");

        $result = $line->parse($ctx);

        echo print_r($result . "", true) . "\n";


        echo "parse009\n";
        $ctx = CTX::create("abcde,      xyz, aa, @abc<>");

        $result = $line->parse($ctx);

        echo print_r($result . "", true) . "\n";

        return $result;
    }

    public function parse005_2()
    {
        echo "=========================================================================\n";
        echo "parse005_2\n";


        //$open = ParserFactory::Token("{");
        // 数値
        $number = ParserFactory::Regex("/^([1-9][0-9]*)|^([0-9])/")->setName("Number");

        // ２項演算子
        $operator = ParserFactory::Char("+-*/@")->setName("Operator");

        // 括弧式
        $parenthesis = ParserFactory::Forward()->setName("Parenthesis");

        // atom := 数値　または　括弧式
        $atom = ParserFactory::Choice()->setName("Atom")->add($number)->add($parenthesis);

        // Expression := atom + (２項演算子＋atom)*
        $expression = ParserFactory::Seq()->setName("Expression")
            ->add($atom)
            ->add(
                ParserFactory::Many()
                    ->add(
                        ParserFactory::Seq()
                            ->add($operator)
                            ->add($atom)
                    )
            );

        // 括弧式 := "(" + Expression + ")"
        $parenthesis->forward(
            ParserFactory::Seq()
                ->add(ParserFactory::Token("(")->setName("Left"))
                ->add($expression)
                ->add(ParserFactory::Token(")")->setName("Right"))
        );


        $ctx = CTX::create("1+2@6-(3+1+((1+2)+1+2-(4+126)/2+3+(1000+2120)*2)-(4*(1@2@3)))");

        $result = $expression->parse($ctx);

        echo print_r($result . "", true) . "\n";
    }

    public function parse005_3()
    {
        echo "=========================================================================\n";
        echo "parse005_3\n";


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

        echo print_r($result . "", true) . "\n";
    }

    public function parse005_4()
    {
        echo "=========================================================================\n";
        echo "parse005_4\n";

        /*
         * Primitive := /^[A-Za-z]|^[A-Za-z_][A-Za-z0-9]+/
         *
         * Value := Primitive | Element | Array
         *
         * Joint := "=>"
         * Key := /^[A-Za-z]|^[A-Za-z_][A-Za-z0-9]+/
         * Entry := Key + Joint + Value
         *
         * Entries := Entry | (Entry , ) + Entry
         *
         * Element := "{" + Entries + "}"
         *
         * Array := "[" + (Value | (Value , ) + Value) + "]"
         *
         *
         */
        //$whiteSpace = ParserFactory::Regex("/^\s+/");
        $whiteSpace = ParserFactory::Option()->add(ParserFactory::Regex("/^\s+/"));
        $lineBreak = ParserFactory::Option()->add(ParserFactory::Token("\n"));

        $whiteSpace = ParserFactory::Seq()->add($whiteSpace)->add($lineBreak)->add($whiteSpace);


        // Joint := "=>"
        $joint = ParserFactory::Token("->")/*->setName("Joint")*/
        ;

        $quote = ParserFactory::Token("\"");

        // Primitive := /^[A-Za-z]|^[A-Za-z_][A-Za-z0-9]+/
        //$primitive = ParserFactory::Regex("/^[A-Za-z]+/")->setName("Pr");
        $primitive = ParserFactory::Seq()->add($quote)->add(ParserFactory::Regex("/^[A-Za-z0-9_]+/")->setName("Pr"))->add($quote);

        // Key := /^[A-Za-z]|^[A-Za-z_][A-Za-z0-9]+/
        //$key = ParserFactory::Regex("/^[A-Za-z]+/")->setName("Key");
        $key = ParserFactory::Seq()->add($quote)->add(ParserFactory::Regex("/^[A-Za-z_]+/")->setName("Key"))->add($quote);

        //

        $value = ParserFactory::Forward();
        $element = ParserFactory::Forward();


        $array = ParserFactory::Forward();


        // Entry := Key + Joint + Value
        $entry = ParserFactory::Seq()
            ->add($whiteSpace)
            ->add($key)
            ->add($whiteSpace)
            ->add($joint)
            ->add($whiteSpace)
            ->add($value)
            ->add($whiteSpace);

        // Entries := (Entry , ) + Entry | Entry
        $entries = ParserFactory::Choice()
            ->add(
                ParserFactory::Seq()->add(
                    ParserFactory::Any()->add(
                        ParserFactory::Seq()
                            ->add($entry)
                            //->add(ParserFactory::Option()->add($whiteSpace))
                            ->add($whiteSpace)
                            ->add(ParserFactory::Token(","))
                            //->add(ParserFactory::Option()->add($whiteSpace))
                            ->add($whiteSpace)
                    )
                )->add(
                    $entry
                )
            )->add($entry);

        // Array := "[" + (Value | (Value , ) + Value) + "]"
        $array->forward(
            ParserFactory::Seq()
                ->add($whiteSpace)
                ->add(ParserFactory::Token("["))
                ->add($whiteSpace)
                ->add(
                    ParserFactory::Choice()
                        ->add(
                            ParserFactory::Seq()->add(
                                ParserFactory::Any()->add(
                                    ParserFactory::Seq()
                                        ->add($value)
                                        //->add(ParserFactory::Option()->add($whiteSpace))
                                        ->add($whiteSpace)
                                        ->add(ParserFactory::Token(","))
                                        //->add(ParserFactory::Option()->add($whiteSpace))
                                        ->add($whiteSpace)
                                )
                            )->add(
                                $value
                            )
                        )->add($value)
                )
                ->add($whiteSpace)
                ->add(ParserFactory::Token("]"))
                ->add($whiteSpace)
        );

        // Value := Primitive | Element | Array
        $value->forward(
            ParserFactory::Choice()
                ->add($primitive)
                ->add($element)
                ->add($array)
        );

        // Element := "{" + Entries + "}"
        $element->forward(
            ParserFactory::Seq()
                ->add($whiteSpace)
                ->add(ParserFactory::Token("{"))
                ->add($whiteSpace)
                ->add($entries)
                ->add($whiteSpace)
                ->add(ParserFactory::Token("}"))
                ->add($whiteSpace)
        );
        $tgt = <<<EOF
{  "aaa"  ->  {  "_bbb" ->   "c_12_cc", "hhhhh" ->{"i"                 ->                 "j"                 }  ,    "fff" -> "gggg"    }  , "ddd" -> "ee" }
EOF;
        $tgt = <<<EOF
                {
                    "aaa" -> 
                           {
                        "_bbb" -> "c_12_cc",
                         "hhhhh" -> {
                            "i" -> "j"
                         },
                         "fff" ->
                            "gggg"
                    },
"ddd" -> "ee",
                    "xyz" -> [
                        "xxa", 
                        "xxb", 
                        [
                            "a", 
                            "b"
                        ], 
                        {
                            "c" -> 
                                        "d"
                        },
                        {
                            "c" -> [
                                "e", 
                                "f"
                            ]
                        }
                    ]
                }
EOF;


        $ctx = CTX::create($tgt);

        $result = $element->parse($ctx);

        echo print_r($result . "", true) . "\n";
    }
}