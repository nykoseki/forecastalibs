<?php

namespace Forecasta;

use Faker\Provider\File;
use Forecasta\Parser as P;
use Forecasta\Parser\ParserContext as CTX;
use Forecasta\Parser\Impl as PImpl;
use Forecasta\Parser\ParserFactory;
use Forecasta\Comment\Processor\CommentParser;
use Forecasta\Parser\Impl\JsonParser;
use Forecasta\Common\ProxyTrait;
use PhpParser\Comment;
use Forecasta\Loader\Xml\XMLLoader;
use Forecasta\Loader\Filesystem;


class ForecastaMain
{
    public function test0001()
    {
        return "test00001-00001テスト2(new)";
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
                            "i" -> "j",
                            "i2" -> true,
                            "i3" -> false,
                            "i4" -> TRUE,
                            "i5" -> FALSE
                         },
                         "fff" ->
                            "gggg"
                    },
                    "ddd"-> 
                            "ee"
                                    ,
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

    public function parse005_5()
    {
        echo "=========================================================================\n";
        echo "parse005_5\n";


        // Element := "{" + Entries + "}"
        $element = (new JsonParser);
        $tgt0 = <<<EOF
{  "aaa"  :  {  "_bbb" :   "c_12_cc", "hhhhh" :{"i"                 :                 "j"                 }  ,    "fff" : "gggg"    }  , "ddd" : "ee" }
EOF;
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

        //$tgt0 = '{"aa":{"bb":["cc","dd",{"ee":"ff"},123]}}';
        $ctx = CTX::create($tgt);

        $time_start = microtime(true);
        $result = $element->parse($ctx);
        $time = microtime(true) - $time_start;

        echo print_r($result . "", true) . "\n";
        //echo print_r($history->isRoot() . "", true) . "\n";

        //echo var_dump($history, true) . "\n";
        echo "{$time} 秒" . PHP_EOL;

        $xml = new XMLLoader();
        //$xml->init("");

        $file = new Filesystem();

        //echo getcwd() . PHP_EOL;
        //echo print_r($file->exists() ? "<Yes>": "<N>", true) . "\n";
        //echo print_r($file->_clear()->src->forecasta->Loader->Test001->_back()->exists() ? "<Yes>": "<N>", true) . "\n";
        //echo print_r($file->_clear()->src->forecasta->Parser->sample->_file("parser001.xml")->exists() ? "<Yes>": "<N>", true) . "\n";

        $file->_clear()
            ->select
            ->all
            ->from
            ->TestMaster
            ->where
            ->get()
            ->exists();
    }

    public function parse006()
    {
        $context = <<<EOF
    /**
     * @aaa "aaaaa"
     * @Test(
     *     "aaa"=>"bbb",
     *     "ccc"=>"ddd"
     * )
     * @bbb("aaaaa" => "bbbbb", "ccccc" => "dddddd")
     * @xyz(
     *     "aaa" => "bbb",
     *     "ccc" => (
     *          "ddd" => "eee",
     *          "fff" => "gggg"
     *      )
     * )
     * @test true
     * @return string
     */
EOF;
        // 解析対象
        $context = (new CommentParser())->normalizeComment($context);

        // プリミティブ := クォート文字列, 数値, True, False, Empty, Null, string
        $primitive = ParserFactory::Choice()->setName("Primitive");
        {// プリミティブ値定義
            // クォート文字列
            $quoted = ParserFactory::Seq()->setName("Quoted")->addAt($primitive);

            // 数値
            $number = ParserFactory::Regex("/^[0-9]+/")->setName("Number")->addAt($primitive);

            // Bool
            $bool = (new PImpl\BoolParser())->setName("Bool")->addAt($primitive);
        }

        $space = ParserFactory::Regex("/^\s+/");
        $whiteSpace = new PImpl\LbWsParser;

        // サブジェクト(@xxx)
        $subject = ParserFactory::Seq()
            ->add(ParserFactory::Token("@"))
            ->add(ParserFactory::Regex("/^[A-Za-z_][A-Za-z0-9]+/")->setName("Subject"));

        // 単一行設定エントリ(@xxx "yyy")
        $singleConf = ParserFactory::Choice()->add(
            ParserFactory::Seq()->setName("Single")
                ->add($subject)
                ->add($space)
                ->add($primitive)
        )
        ->add(
            // 標準ドキュメントタグなど(指定された設定エントリは読み飛ばし)
            ParserFactory::Seq()->setName("DefaultTag")->skip(true)
        );

        // KeyValueEntry | KeyValueEntries
        $compositeElement = ParserFactory::Forward();


        $compositeConfigurationDelim = ParserFactory::Seq()
            ->add(ParserFactory::Token("=>"));

        // (compositeElement, compositeElement) + compositeElement | compositeElement
        $compositeElements = ParserFactory::Choice()
            ->add(
                ParserFactory::Seq()
                    ->add(
                        ParserFactory::Any()
                            ->add($compositeElement)
                            ->add($whiteSpace)
                            ->add(ParserFactory::Token(","))
                            ->add($whiteSpace)
                    )
                    ->add($compositeElement)
            )
            ->add($compositeElement)
            ->setName("Configurations");

        // 複数行設定エントリ
        // @xxx(
        //  $compositeConfItem
        // )
        $multiConf = ParserFactory::Seq()
            ->add(ParserFactory::Token("("))
            ->add($whiteSpace)
            ->add($compositeElement)
            ->add($whiteSpace)
            ->add(ParserFactory::Token(")"));

        // 設定(述語)エントリ(単一行設定(述語)エントリ or 複数行設定(述語)エントリ or 述語無し設定エントリ)
        $configuration = ParserFactory::Choice()->setName("Configuration")
            ->add($multiConf)
            ->add($singleConf)
            //->add($subject)
        ;

        // 設定エントリデリミタ
        $configurationDelim = ParserFactory::Seq()
            ->add(ParserFactory::Regex("/^\n/"))
            ->add(new PImpl\LbWsParser);

        // 設定エントリ群
        $configurations = ParserFactory::Choice()
            ->add(
                ParserFactory::Seq()
                    ->add(
                        ParserFactory::Any()
                            ->add($configuration)
                            ->add($configurationDelim)
                    )
                    ->add($configuration)
            )
            ->add($configuration)
            ->setName("Configurations");

        /*
        $result = $configurations->parse(CTX::create($context));
        echo print_r($result . "", true) . "\n";
        */
    }


    public function parse007() {
        //$target = "AAXX***312604806YYZZ<{222:{123:{456:789},888:098},223:334,444:{12:34}}>dcc";
        $target = "{111:222,333:444,444:{555:{556:{9:{10:{11:12,13:14}}}}},777:888}";

        $composite = ParserFactory::Forward()->setDebug(true)->setName("Root");
        $key = ParserFactory::Regex("/^[0-9]+/")->setDebug(true)->setName("Key");

        $primitive = ParserFactory::Regex("/^[0-9]+/")->setDebug(true)->setName("Primitive");

        $delim = ParserFactory::Token(":")->setName("Delimiter")->setDebug(true);

        $value = ParserFactory::Choice()->setName("Value")->setDebug(true);
        $value->add($primitive);
        $value->add($composite);

        $keyValue = ParserFactory::Seq()->setDebug(true)->setName("Entry");
        $keyValue->add($key)->add($delim)->add($value);

        $composite->forward(
            ParserFactory::Seq()->setName("Container")->setDebug(true)
                ->add(ParserFactory::Token("{")->setDebug(true)->setName("Open"))
                ->add(
                    ParserFactory::Choice()->setDebug(true)->setName("ContainerInner")
                        ->add(
                            ParserFactory::Seq()->setDebug(true)->setName("Elements")
                                ->add(
                                    ParserFactory::Any()->setDebug(true)->setName("EntryAny")
                                        ->add(
                                            ParserFactory::Seq()->setDebug(true)->setName("Entries")
                                                ->add($keyValue)
                                                ->add(ParserFactory::Token(",")->setDebug(true)->setName("Comma"))
                                        )
                                )
                                ->add($keyValue)
                        )
                        ->add($keyValue)
                )
                ->add(ParserFactory::Token("}")->setDebug(true)->setName("Close"))
        );

        $base = CTX::create($target);

        // 構文解析スタート
        //$result = $parser->parse($base);

        $time_start = microtime(true);
        $result = $composite->parse($base);
        $time = microtime(true) - $time_start;
        echo "{$time} 秒" . PHP_EOL;

        $target = $result->target();
        $len = mb_strlen($target);
        if(mb_strlen($target) !== $result->current()) {
            echo "Non Parsed!!!({$result->current()}, {$len})\n";
        }

        echo print_r($result . "", true) . "\n";
    }
}
